<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\Unemployment\Entity\Abstracts;

use Eureka\Component\Orm\EntityInterface;
use Eureka\Component\Orm\Traits;
use Eureka\Component\Validation\Exception\ValidationException;
use Eureka\Component\Validation\ValidatorFactoryInterface;
use Eureka\Component\Validation\ValidatorEntityFactoryInterface;
use Application\Domain\Unemployment\Entity\Unemployment;
use Application\Domain\Unemployment\Repository\UnemploymentRepositoryInterface;
use Eureka\Component\Orm\Exception\OrmException;
use Application\Domain\Unemployment\Entity\UnemploymentValue;
use Application\Domain\Unemployment\Infrastructure\Mapper\UnemploymentValueMapper;

/**
 * Abstract Unemployment data class.
 *
 * /!\ AUTO GENERATED FILE. DO NOT EDIT THIS FILE.
 * You can add your specific code in child class: Unemployment
 *
 * @author Eureka Orm Generator
 */
abstract class AbstractUnemployment implements EntityInterface
{
    /** @use Traits\EntityTrait<UnemploymentRepositoryInterface, Unemployment> */
    use Traits\EntityTrait;

    /** @var int $id Property id */
    protected int $id = 0;

    /** @var int $groupId Property groupId */
    protected int $groupId = 0;

    /** @var int $genderId Property genderId */
    protected int $genderId = 0;

    /** @var string $ageRange Property ageRange */
    protected string $ageRange = '';

    /** @var bool $isMetropolitan Property isMetropolitan */
    protected bool $isMetropolitan = false;

    /** @var bool $isRate Property isRate */
    protected bool $isRate = false;

    /** @var int $graphId Property graphId */
    protected int $graphId = 0;

    /** @var UnemploymentValue[]|null $joinManyCacheValues Property joinManyCacheValues */
    protected ?array $joinManyCacheValues = null;

    /**
     * AbstractEntity constructor.
     *
     * @param UnemploymentRepositoryInterface $repository
     * @param ValidatorFactoryInterface|null $validatorFactory
     * @param ValidatorEntityFactoryInterface|null $validatorEntityFactory
     */
    public function __construct(
        UnemploymentRepositoryInterface $repository,
        ?ValidatorFactoryInterface $validatorFactory = null,
        ?ValidatorEntityFactoryInterface $validatorEntityFactory = null,
    ) {
        $this->setRepository($repository);
        $this->setValidatorFactories($validatorFactory, $validatorEntityFactory);

        $this->initializeValidatorConfig();
    }

    protected function initializeValidatorConfig(): void
    {
        $this->setValidatorConfig([
            'unemployment_id' => [
                'type'      => 'integer',
                'options'   => ['min_range' => 0, 'max_range' => 4294967295],
            ],
            'unemployment_group_id' => [
                'type'      => 'integer',
                'options'   => ['min_range' => 0, 'max_range' => 255],
            ],
            'gender_id' => [
                'type'      => 'integer',
                'options'   => ['min_range' => 0, 'max_range' => 255],
            ],
            'unemployment_age_range' => [
                'type'      => 'string',
                'options'   => ['min_length' => 0, 'max_length' => 6],
            ],
            'unemployment_is_metropolitan' => [
                'type'      => 'boolean',
                'options'   => [],
            ],
            'unemployment_is_rate' => [
                'type'      => 'boolean',
                'options'   => [],
            ],
            'unemployment_graph_id' => [
                'type'      => 'integer',
                'options'   => ['min_range' => 0, 'max_range' => 65535],
            ],
        ]);
    }

    /**
     * Get cache key
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return 'unemployment.' . $this->getId();
    }

    /**
     * Get value for property "unemployment_id"
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get value for property "unemployment_group_id"
     *
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * Get value for property "gender_id"
     *
     * @return int
     */
    public function getGenderId(): int
    {
        return $this->genderId;
    }

    /**
     * Get value for property "unemployment_age_range"
     *
     * @return string
     */
    public function getAgeRange(): string
    {
        return $this->ageRange;
    }

    /**
     * Get value for property "unemployment_is_metropolitan"
     *
     * @return bool
     */
    public function isMetropolitan(): bool
    {
        return $this->isMetropolitan;
    }

    /**
     * Get value for property "unemployment_is_rate"
     *
     * @return bool
     */
    public function isRate(): bool
    {
        return $this->isRate;
    }

    /**
     * Get value for property "unemployment_graph_id"
     *
     * @return int
     */
    public function getGraphId(): int
    {
        return $this->graphId;
    }

    /**
     * Set value for property "unemployment_id"
     *
     * @param  int $id
     * @return $this
     * @throws ValidationException
     */
    public function setId(int $id): self
    {
        $this->validateInput('unemployment_id', $id);

        if ($this->exists() && $this->id !== $id) {
            $this->markFieldAsUpdated('id');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * Set auto increment value.
     *
     * @param  integer $id
     * @return $this
     * @throws ValidationException
     */
    public function setAutoIncrementId(int $id): static
    {
        return $this->setId($id);
    }

    /**
     * Set value for property "unemployment_group_id"
     *
     * @param  int $groupId
     * @return $this
     * @throws ValidationException
     */
    public function setGroupId(int $groupId): self
    {
        $this->validateInput('unemployment_group_id', $groupId);

        if ($this->exists() && $this->groupId !== $groupId) {
            $this->markFieldAsUpdated('groupId');
        }

        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Set value for property "gender_id"
     *
     * @param  int $genderId
     * @return $this
     * @throws ValidationException
     */
    public function setGenderId(int $genderId): self
    {
        $this->validateInput('gender_id', $genderId);

        if ($this->exists() && $this->genderId !== $genderId) {
            $this->markFieldAsUpdated('genderId');
        }

        $this->genderId = $genderId;

        return $this;
    }

    /**
     * Set value for property "unemployment_age_range"
     *
     * @param  string $ageRange
     * @return $this
     * @throws ValidationException
     */
    public function setAgeRange(string $ageRange): self
    {
        $this->validateInput('unemployment_age_range', $ageRange);

        if ($this->exists() && $this->ageRange !== $ageRange) {
            $this->markFieldAsUpdated('ageRange');
        }

        $this->ageRange = $ageRange;

        return $this;
    }

    /**
     * Set value for property "unemployment_is_metropolitan"
     *
     * @param  bool $isMetropolitan
     * @return $this
     * @throws ValidationException
     */
    public function setIsMetropolitan(bool $isMetropolitan): self
    {
        $this->validateInput('unemployment_is_metropolitan', $isMetropolitan);

        if ($this->exists() && $this->isMetropolitan !== $isMetropolitan) {
            $this->markFieldAsUpdated('isMetropolitan');
        }

        $this->isMetropolitan = $isMetropolitan;

        return $this;
    }

    /**
     * Set value for property "unemployment_is_rate"
     *
     * @param  bool $isRate
     * @return $this
     * @throws ValidationException
     */
    public function setIsRate(bool $isRate): self
    {
        $this->validateInput('unemployment_is_rate', $isRate);

        if ($this->exists() && $this->isRate !== $isRate) {
            $this->markFieldAsUpdated('isRate');
        }

        $this->isRate = $isRate;

        return $this;
    }

    /**
     * Set value for property "unemployment_graph_id"
     *
     * @param  int $graphId
     * @return $this
     * @throws ValidationException
     */
    public function setGraphId(int $graphId): self
    {
        $this->validateInput('unemployment_graph_id', $graphId);

        if ($this->exists() && $this->graphId !== $graphId) {
            $this->markFieldAsUpdated('graphId');
        }

        $this->graphId = $graphId;

        return $this;
    }

    /**
     * Get list of UnemploymentValue entities instance.
     *
     * @param  bool $isForceReload
     * @return UnemploymentValue[]
     * @throws OrmException
     */
    public function getAllValues(bool $isForceReload = false): array
    {
        if ($isForceReload || $this->joinManyCacheValues === null) {
            /** @phpstan-var UnemploymentValueMapper $mapper */
            $mapper = $this->getRepository()->getMapper(UnemploymentValueMapper::class);
            $this->joinManyCacheValues = $mapper->findAllByKeys([
                'unemployment_id' => $this->getId(),
            ]);
        }

        return $this->joinManyCacheValues;
    }

    /**
     * Set UnemploymentValue entity instances.
     *
     * @param UnemploymentValue[] $entities
     * @return $this
     */
    public function setAllValues(array $entities): self
    {
        $this->joinManyCacheValues = $entities;

        return $this;
    }
}
