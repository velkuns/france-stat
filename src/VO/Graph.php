<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\VO;

class Graph
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $valueName,
    ) {}
}
