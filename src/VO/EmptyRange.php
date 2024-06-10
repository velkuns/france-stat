<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\VO;

class EmptyRange extends Range
{
    public function __construct(
    ) {
        parent::__construct(0, 0);
    }
}
