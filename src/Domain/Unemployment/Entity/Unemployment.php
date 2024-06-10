<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Domain\Unemployment\Entity;

use Application\Enum\Gender;
use Application\Enum\UnemploymentGroup;
use Application\VO\Dataset;
use Eureka\Component\Orm\Exception\OrmException;

/**
 * DataMapper Data class for table "unemployment"
 *
 * @author Eureka Orm Generator
 */
class Unemployment extends Abstracts\AbstractUnemployment
{
    public function isMales(): bool
    {
        return $this->getGenderId() === Gender::Male->value;
    }

    public function isFemales(): bool
    {
        return $this->getGenderId() === Gender::Male->value;
    }

    public function isWholeRange(): bool
    {
        return $this->getAgeRange() === '15-64';
    }

    public function getLabel(): string
    {
        $label = Gender::from($this->getGenderId())->label();
        if ($this->getAgeRange() !== '15-64') {
            $label .= ' - ' . $this->getAgeRange();
        }

        return $label;
    }

    /**
     * @throws OrmException
     */
    public function getDataset(): Dataset
    {
        return new Dataset($this->getLabel(), $this->getData(), $this->getColor());
    }

    public function getColor(): string
    {
        $cc = $this->getRangeColor();
        return match($this->getGenderId()) {
            Gender::Ensemble->value => '#999999',
            Gender::Female->value   => "#{$cc}0000",
            Gender::Male->value     => "#0000{$cc}",
            Gender::Other->value    => "#00{$cc}00",
            default                 => "#{$cc}00{$cc}",
        };
    }

    /**
     * @return list<int|float>
     * @throws OrmException
     */
    public function getData(): array
    {
        $dataset = [];
        foreach ($this->getAllValues() as $value) {
            $dataset[] = $value->getNumberReal();
        }

        return $dataset;
    }

    /**
     * @return list<string>
     * @throws OrmException
     */
    public function getLabels(): array
    {
        $labels = [];
        foreach ($this->getAllValues() as $value) {
            $labels[] = $value->getYear() . '-T' . $value->getQuarter();
        }

        return $labels;
    }

    public function getGraphName(): string
    {
        return
            UnemploymentGroup::from($this->getGroupId())->label() .
            ($this->isMetropolitan ? ' - France métropolitaine' : ' - France (Hors Mayotte)')
        ;
    }

    public function getGraphValueName(): string
    {
        return $this->isRate() ? 'Valeur en %' : 'Valeur en milliers';
    }

    private function getRangeColor(): string
    {
        return match ($this->getAgeRange()) {
            '15-64' => 'FF',
            '15-24' => 'CC',
            '25-49' => 'AA',
            '50-64' => '88',
            default => '99',
        };
    }
}
