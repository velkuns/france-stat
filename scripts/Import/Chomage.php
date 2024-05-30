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
        $csv = new FileCsv(
            self::BASE_PATH . '/' . $this->options()
                ->value('f', 'file')
        );
        $csv->setCsvControl(separator: ";");
        $csv->skipHeader();

        \var_export($csv->getHeader());

        /** @var array<int, string> $line */
        foreach ($csv as $line) {
            $label = $line[0];
            if ($label === 'Codes' || \str_ends_with($label, 'Série arrêtée') || \str_contains($label, 'Ensemble')) {
                continue;
            }

            if (\str_contains($label, ' (en milliers)')) {
                $isPercent = false;
                $label = \str_replace(' (en milliers)', '', $label);
            } elseif (\str_contains($label, ' part dans la population')) {
                $isPercent = true;
                $label = \str_replace(' (part dans la population)', '', $label);
            } else {
                $isPercent = true;
            }

            [$group, $segment, $region,] = \explode(' - ', $label);

            $isMetropolitan = $region == 'France métropolitaine';
            if (\str_contains($segment, ' ')) {
                [$gender, $segment] = \explode(' ', $segment, 2);
            } else {
                continue;
                //$gender  = $segment;
                //$segment = '';
            }

            var_export(
                [
                    'label'           => $line[0],
                    'group'           => $group,
                    'gender'          => $gender,
                    'segment'         => $segment,
                    'is_metropolitan' => $isMetropolitan,
                    'is_percent'      => $isPercent,
                ]
            );
            //$this->output()->writeln($label);
        }
    }
}
