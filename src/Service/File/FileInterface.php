<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\File;

interface FileInterface
{
    /**
     * @param resource|null $context
     */
    public function openFile(string $mode = 'r', bool $useIncludePath = false, $context = null): \SplFileObject|File;

    public function getPathInfo(?string $class = null): ?\SplFileInfo;

    public function getFileInfo(?string $class = null): \SplFileInfo;
}
