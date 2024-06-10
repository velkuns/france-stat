<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Enum;

enum Gender: int
{
    case Ensemble = 0;
    case Male = 1;
    case Female = 2;
    case Other = 3;

    public static function fromLabel(string $label): self
    {
        return match (strtolower($label)) {
            'ensemble', 'ensembles' => self::Ensemble,
            'homme', 'hommes'       => self::Male,
            'femme', 'femmes'       => self::Female,
            default                 => self::Other,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Ensemble => 'Ensemble',
            self::Male     => 'Hommes',
            self::Female   => 'Femmes',
            self::Other    => 'Autres',
        };
    }
}
