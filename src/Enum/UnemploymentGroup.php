<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Enum;

enum UnemploymentGroup: int
{
    case Rate = 1;
    case Number = 2;
    case RateLong = 3;
    case NumberLong = 4;
    case NumberInHalo = 5;

    private const LABELS = [
        1 => 'Taux de chômage au sens du BIT',
        2 => 'Chômeurs au sens du BIT',
        3 => 'Taux de chômage de longue durée',
        4 => 'Chômeurs de longue durée',
        5 => 'Personnes dans le halo autour du chômage',
    ];

    public static function fromLabel(string $label): self
    {
        $data = array_flip(self::LABELS);

        if (!isset($data[$label])) {
            throw new \TypeError("Label not found ! (label: $label)");
        }

        return self::from($data[$label]);
    }

    public function label(): string
    {
        return self::LABELS[$this->value];
    }
}
