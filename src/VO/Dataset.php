<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\VO;

class Dataset implements \JsonSerializable
{
    /**
     * @param list<int|float> $data
     */
    public function __construct(
        public readonly string $label,
        public array $data,
        public readonly string $borderColor,
        public readonly bool $fill = false,
        public readonly float $tension = 0.4,
        public readonly string $cubicInterpolationMode = 'monotone',
    ) {}

    /**
     * @return array{
     *     label: string,
     *     data: list<int|float>,
     *     borderColor: string,
     *     fill: bool,
     *     tension: float,
     *     cubicInterpolationMode: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'label'                  => $this->label,
            'data'                   => $this->data,
            'borderColor'            => $this->borderColor,
            'fill'                   => $this->fill,
            'tension'                => $this->tension,
            'cubicInterpolationMode' => $this->cubicInterpolationMode,
        ];
    }
}
