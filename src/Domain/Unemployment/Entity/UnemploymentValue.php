<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\Unemployment\Entity;

use Application\Domain\Unemployment\Repository\UnemploymentValueRepositoryInterface;

/**
 * DataMapper Data class for table "unemployment_value"
 *
 * @author Eureka Orm Generator
 */
class UnemploymentValue extends Abstracts\AbstractUnemploymentValue
{
    public function getNumberReal(): int|float
    {
        return $this->getNumber() * $this->multiplicator;
    }
}
