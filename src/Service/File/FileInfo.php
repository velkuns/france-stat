<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\File;

use Application\Service\File\Exception\FileException;

class FileInfo extends \SplFileInfo
{
    public function openFile(string $mode = 'r', $useIncludePath = false, $context = null): File
    {
        $this->setFileClass(File::class);

        /** @var File $file */
        $file = parent::openFile($mode, $useIncludePath, $context);

        return $file;
    }

    public function getPathInfo($class = null): \SplFileInfo|FileInfo|null
    {
        if ($class === null) {
            $class = FileInfo::class;
        }

        $this->setInfoClass($class);

        return parent::getPathInfo();
    }

    public function getFileInfo($class = null): \SplFileInfo|FileInfo
    {
        if ($class === null) {
            $class = FileInfo::class;
        }

        $this->setInfoClass($class);

        return parent::getFileInfo();
    }

    /**
     * @throws \RuntimeException
     */
    public function remove(): bool
    {
        if ($this->isDir() && $this->getFilename() === '..') {
            throw new \RuntimeException("Cannot remove parent directory (file: {$this->getFilename()} !", 10001);
        }

        if ($this->isDir()) {
            $removed = \rmdir($this->getPath());
        } else {
            $removed = \unlink($this->getPathname());
        }

        if (!$removed) {
            throw new FileException("Cannot remove file / directory (file: {$this->getPathname()}) !", 10002);
        }

        return true;
    }
}
