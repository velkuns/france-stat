<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\Unemployment\Infrastructure\Mapper;

use Eureka\Component\Orm\Traits;
use Application\Domain\Unemployment\Entity\UnemploymentValue;
use Application\Domain\Unemployment\Repository\UnemploymentValueRepositoryInterface;

/**
 * Mapper class for table "unemployment_value"
 *
 * @author Eureka Orm Generator
 */
class UnemploymentValueMapper extends Abstracts\AbstractUnemploymentValueMapper implements UnemploymentValueRepositoryInterface
{
    /** @use Traits\RepositoryTrait<UnemploymentValueRepositoryInterface, UnemploymentValue> */
    use Traits\RepositoryTrait;
}
