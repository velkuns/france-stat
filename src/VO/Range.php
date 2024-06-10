<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\VO;

class Range implements \Stringable
{
    public function __construct(
        public readonly int $min,
        public readonly int $max,
    ) {}

    public function __toString(): string
    {
        return "$this->min-$this->max";
    }
}
