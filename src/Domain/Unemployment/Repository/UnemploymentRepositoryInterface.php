<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\Unemployment\Repository;

use Eureka\Component\Orm\RepositoryInterface;
use Application\Domain\Unemployment\Entity\Unemployment;

/**
 * Unemployment repository interface.
 *
 * @author Eureka Orm Generator
 *
 * @extends RepositoryInterface<Unemployment>
 */
interface UnemploymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array<Unemployment>
     */
    public function findAll(): array;

    /**
     * @return list<Unemployment>
     */
    public function findAllGroupedByGraphIds(): array;

    /**
     * @return array<Unemployment>
     */
    public function findAllByGraphId(int $graphId): array;
}
