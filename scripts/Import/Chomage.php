<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Import;

use Application\Service\File\FileCsv;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Help;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;

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
class Chomage extends AbstractScript
{
    protected const BASE_PATH = __DIR__ . '/../../data/chomage';

    public function __construct()
    {
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

    public function run(): void
    {
        if (empty($this->options()->value('file', 'f'))) {
            throw new \UnexpectedValueException('Missing file option', 1000);
        }

        $csv = new FileCsv(
            self::BASE_PATH . '/' . $this->options()
                ->value('f', 'file'),
        );
        $csv->setCsvControl(separator: ";");
        $csv->skipHeader();

        //\var_export($csv->getHeader());

        /** @var array<int, string> $line */
        foreach ($csv as $line) {
            $label = $line[0];
            if ($this->isNonInterestingLine($label)) {
                continue;
            }

            //~ Check if percentage + clean label if needed
            $isPercentage = $this->isPercentage($label);

            //~ Parse group, segment & region
            [$group, $segment, $region,] = \explode(' - ', $label);

            //~ Check if it is metropolitan or global (excluding Mayotte)
            $isMetropolitan = $this->isMetropolitan($region);

            if (!\str_contains($segment, ' ')) {
                continue;
            }

            //~ Parse gender (or type "Inactif") + segment
            [$gender, $segment] = \explode(' ', $segment, 2);

            //~ Clean segment
            $segment  = \str_replace(' (part dans la population de 15 ans ou plus)', '', $segment);
            $segment  = \str_replace(' (part de la population de 15 à 64 ans)', '', $segment);
            $segment  = \str_replace(' (part dans la population)', '', $segment);
            $segments = $this->parseSegment($segment);

            if (empty($segments)) {
                continue;
            }

            var_export(
                [
                    //'label'           => $line[0],
                    'group'           => $group,
                    'gender'          => $gender,
                    //'segment'         => $segment,
                    'segments'        => $segments,
                    'is_metropolitan' => $isMetropolitan,
                    'is_percentage'   => $isPercentage,
                ],
            );
            //$this->output()->writeln($label);
        }
    }

    private function isNonInterestingLine(string $label): bool
    {
        return $label === 'Codes' ||
            \str_ends_with($label, 'Série arrêtée') ||
            \str_contains($label, 'Ensemble') ||
            (!\str_contains($label, 'Hommes') && !\str_contains($label, 'Femmes') && !\str_contains($label, 'Inactifs'))
        ;
    }

    private function isPercentage(string &$label): bool
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

    /**
     * @return array{0?: int, 1?: int}
     */
    private function parseSegment(string $segment): array
    {
        return match($segment) {
            'de moins de 25 ans', 'de 15 à 24 ans' => [15, 24],
            'de 25 à 49 ans'                       => [25, 49],
            'de 50 ans ou plus', 'de 50 à 64 ans'  => [50, 64],
            default                                => [],
        };
    }
}
