<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\Unemployment\Infrastructure\Mapper;

use Eureka\Component\Orm\Exception\OrmException;
use Eureka\Component\Orm\Query\SelectBuilder;
use Eureka\Component\Orm\Traits;
use Application\Domain\Unemployment\Entity\Unemployment;
use Application\Domain\Unemployment\Repository\UnemploymentRepositoryInterface;

/**
 * Mapper class for table "unemployment"
 *
 * @author Eureka Orm Generator
 */
class UnemploymentMapper extends Abstracts\AbstractUnemploymentMapper implements UnemploymentRepositoryInterface
{
    /** @use Traits\RepositoryTrait<UnemploymentRepositoryInterface, Unemployment> */
    use Traits\RepositoryTrait;

    /**
     * @return array<Unemployment>
     * @throws OrmException
     */
    public function findAll(): array
    {
        return $this->select(new SelectBuilder($this));
    }

    /**
     * @return array<Unemployment>
     * @throws OrmException
     */
    public function findAllByGraphId(int $graphId): array
    {
        $builder = (new SelectBuilder($this))
            ->addWhere('unemployment_graph_id', $graphId)
        ;

        return $this->select($builder);
    }

    /**
     * @return list<Unemployment>
     * @throws OrmException
     */
    public function findAllGroupedByGraphIds(): array
    {
        $builder = (new SelectBuilder($this))
            ->addGroupBy('unemployment_graph_id')
        ;

        return $this->select($builder);
    }
}
