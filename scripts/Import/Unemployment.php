<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Import;

use Application\Domain\Unemployment\Entity\Unemployment as UnemploymentEntity;
use Application\Domain\Unemployment\Repository\UnemploymentRepositoryInterface;
use Application\Domain\Unemployment\Repository\UnemploymentValueRepositoryInterface;
use Application\Enum\Gender;
use Application\Enum\UnemploymentGroup;
use Application\Service\File\FileCsv;
use Application\VO\EmptyRange;
use Application\VO\Range;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Help;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Eureka\Component\Orm\Exception\EntityNotExistsException;
use Eureka\Component\Orm\Exception\OrmException;
use Psr\Clock\ClockInterface;

/**
 * Run command with:
 * <code>
 * Use    : bin/console import/chomage [OPTION]...
 * OPTIONS:
 * -o ARG, --option=ARG                  Option description
 * </code>
 *
 * @codeCoverageIgnore
 */
class Unemployment extends AbstractScript
{
    public function __construct(
        private readonly UnemploymentRepositoryInterface $unemploymentRepository,
        private readonly UnemploymentValueRepositoryInterface $unemploymentValueRepository,
        private readonly ClockInterface $clock,
        private readonly string $dataPath,
    ) {
        $this->setDescription('Example script');
        $this->setExecutable();

        $this->initOptions(
            (new Options())
                ->add(
                    new Option(
                        shortName:   'f',
                        longName:    'file',
                        description: 'File to import',
                        mandatory:   true,
                        hasArgument: true,
                        default:     '',
                    ),
                )
                ->add(
                    new Option(
                        shortName:   'd',
                        longName:    'dry-run',
                        description: 'Do not import, on parse file',
                        default:     false,
                    ),
                ),
        );
    }

    public function help(): void
    {
        (new Help(
            substr(self::class, (int) strrpos(self::class, '\\') + 1),
            $this->declaredOptions(),
            $this->output(),
            $this->options(),
        ))->display();
    }

    /**
     * @throws OrmException
     */
    public function run(): void
    {
        if (empty($this->options()->value('file', 'f'))) {
            throw new \UnexpectedValueException('Missing file option', 1000);
        }

        $csv = new FileCsv(
            $this->dataPath . '/' . $this->options()
                ->value('f', 'file'),
        );
        $csv->setCsvControl(separator: ";");
        $csv->skipHeader();

        //\var_export($csv->getHeader());

        $headers = $csv->getHeader();
        $dates   = array_slice($headers, 4);

        /** @var array<int, string> $line */
        foreach ($csv as $line) {
            $label = (string) \array_shift($line);

            if ($this->isNonInterestingLine($label)) {
                //echo "label: $label\n";
                continue;
            }

            //~ Check if percentage + clean label if needed
            $isRate = $this->isRate($label);

            //~ Parse group, segment & region
            [$group, $segment, $region,] = \explode(' - ', $label);

            //~ Check if it is metropolitan or global (excluding Mayotte)
            $isMetropolitan = $this->isMetropolitan($region);

            if (!\str_contains($segment, ' ')) {
                $gender = $segment;
                $ages  = new Range(15, 64);
            } else {
                //~ Parse gender (or type "Inactif") + segment
                [$gender, $agesString] = \explode(' ', $segment, 2);
                $ages = $this->parseAges($agesString);
            }

            if ($ages instanceof EmptyRange) {
                continue;
            }

            $graphId = UnemploymentGroup::fromLabel($group)->value . (int) $isMetropolitan . (int) $isRate;

            $data = [
                'unemployment_group_id'        => UnemploymentGroup::fromLabel($group)->value,
                'gender_id'                    => Gender::fromLabel($gender)->value,
                'unemployment_age_range'       => (string) $ages,
                'unemployment_is_metropolitan' => $isMetropolitan,
                'unemployment_is_rate'         => $isRate,
                'unemployment_graph_id'        => (int) $graphId,
            ];

            $unemployment = $this->getUnemploymentEntity($data);

            $multiplicator = $isRate ? 0.1 : 1;

            \array_shift($line); // code
            $dateOrigin = \DateTimeImmutable::createFromFormat('d/m/Y H:i', (string) \array_shift($line));
            if (!$dateOrigin instanceof \DateTimeImmutable) {
                $dateOrigin = $this->clock->now();
            }
            \array_shift($line); // empty

            foreach ($dates as $index => $date) {
                [$year, $quarter] = \explode('-T', $date);

                $value = (int) ($isRate ? ((float) $line[$index]) * 10 : $line[$index]);

                if ($value == 0) {
                    continue;
                }

                try {
                    $unemploymentValue = $this->unemploymentValueRepository->findByKeys(
                        [
                            'unemployment_id'            => $unemployment->getId(),
                            'unemployment_value_year'    => (int) $year,
                            'unemployment_value_quarter' => (int) $quarter,
                        ],
                    );
                } catch (EntityNotExistsException) {
                    $unemploymentValue = $this->unemploymentValueRepository->newEntity();
                    $unemploymentValue->setUnemploymentId($unemployment->getId());
                }

                $unemploymentValue->setYear((int) $year);
                $unemploymentValue->setQuarter((int) $quarter);
                $unemploymentValue->setNumber($value);
                $unemploymentValue->setMultiplicator($multiplicator);
                $unemploymentValue->setDateOrigin($dateOrigin->format('Y-m-d H:i:s'));
                $unemploymentValue->setDateUpdate($this->clock->now()->format('Y-m-d H:i:s'));

                if (!$this->options()->value('dry-run', 'd')) {
                    $this->unemploymentValueRepository->persist($unemploymentValue);
                }
            }
        }
    }

    private function isNonInterestingLine(string $label): bool
    {
        return $label === 'Codes' ||
            \str_ends_with($label, 'Série arrêtée') ||
            (
                !\str_contains($label, 'Hommes') &&
                !\str_contains($label, 'Femmes') &&
                !\str_contains($label, 'Inactifs') &&
                !\str_contains($label, 'Ensemble')
            )
        ;
    }

    private function isRate(string &$label): bool
    {
        if (\str_contains($label, ' (en milliers)')) {
            $label = \str_replace(' (en milliers)', '', $label);
            return false;
        } elseif (\str_contains($label, ' part dans la population')) {
            $label = \str_replace(' (part dans la population)', '', $label);
            return true;
        }

        return true;
    }

    private function isMetropolitan(string $region): bool
    {
        return $region === 'France métropolitaine';
    }

    private function parseAges(string $agesString): Range|EmptyRange
    {
        $agesString = \str_replace(' (part dans la population de 15 ans ou plus)', '', $agesString);
        $agesString = \str_replace(' (part de la population de 15 à 64 ans)', '', $agesString);
        $agesString = \str_replace(' (part dans la population)', '', $agesString);

        return match ($agesString) {
            'de moins de 25 ans', 'de 15 à 24 ans' => new Range(15, 24),
            'de 25 à 49 ans' => new Range(25, 49),
            'de 50 ans ou plus', 'de 50 à 64 ans' => new Range(50, 64),
            default => new EmptyRange(),
        };
    }

    /**
     * @param array{
     *     unemployment_group_id: int,
     *     gender_id: int,
     *     unemployment_age_range: string,
     *     unemployment_is_metropolitan: bool,
     *     unemployment_is_rate: bool,
     *     unemployment_graph_id: int,
     * } $data
     * @throws OrmException
     */
    private function getUnemploymentEntity(array $data): UnemploymentEntity
    {
        try {
            $unemployment = $this->unemploymentRepository->findByKeys(
                [
                    'unemployment_group_id'        => $data['unemployment_group_id'],
                    'gender_id'                    => $data['gender_id'],
                    'unemployment_age_range'       => $data['unemployment_age_range'],
                    'unemployment_is_metropolitan' => $data['unemployment_is_metropolitan'],
                    'unemployment_is_rate'         => $data['unemployment_is_rate'],
                ],
            );
        } catch (EntityNotExistsException) {
            var_export((object) $data);
            $unemployment = $this->unemploymentRepository->newEntity((object) $data);
            if (!$this->options()->value('dry-run', 'd')) {
                $this->unemploymentRepository->persist($unemployment);
            }
        }

        return $unemployment;
    }
}
