<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\File;

use Application\Service\File\Exception\FileNotFoundException;

class File extends \SplFileObject implements FileInterface
{
    /** @var string */
    public const BOM_CHARACTER = "\xEF\xBB\xBF";
    protected bool $isGzCompressed = false;
    protected bool $removeBOM = false;

    public function __construct(
        string $fileName,
        string $mode = 'r',
        bool $useIncludePath = false,
        $context = null,
        bool $isGzCompressed = false
    ) {
        $this->isGzCompressed = $isGzCompressed;

        if ($mode === 'r' && !file_exists($fileName)) {
            throw new FileNotFoundException("The file $fileName doesn't exist");
        }

        if ($this->isGzCompressed) {
            $fileName = 'compress.zlib://' . $fileName;
        }

        parent::__construct($fileName, $mode, $useIncludePath, $context);
    }

    /**
     * @param resource $context Refer to the context section of the manual for a description of contexts.
     */
    public function openFile(string $mode = 'r', bool $useIncludePath = false, $context = null): File
    {
        $this->setFileClass(File::class);

        /** @var File $file */
        $file = parent::openFile($mode, $useIncludePath, $context);

        return $file;
    }

    /**
     * Overridden parent method. Force return type of object to instance of class 'FileInfo', or specified type passed in argument.
     */
    public function getPathInfo(string|null $class = null): \SplFileInfo|FileInfo|null
    {
        if ($class === null) {
            $class = FileInfo::class;
        }

        $this->setInfoClass($class);

        return parent::getPathInfo();
    }

    /**
     * Override parent method. Force return type of object to instance of class 'FileInfo', or specified type passed in argument.
     */
    public function getFileInfo(string|null $class = null): \SplFileInfo|FileInfo
    {
        if ($class === null) {
            $class = FileInfo::class;
        }

        $this->setInfoClass($class);

        return parent::getFileInfo();
    }

    /**
     * Overridden parent method.
     * When file is gz compressed, use gzip command to extract real file size if possible.
     * Otherwise, return an estimation of the size (*5 compressed size)
     */
    public function getSize(): int
    {
        if (!$this->isGzCompressed) {
            return (int) parent::getSize();
        }

        //~ Override for gz compressed files
        $file   = \str_replace('compress.zlib://', '', $this->getPathname());
        $output = [];
        $return = null;
        $string = (string) exec('gzip --list ' . \escapeshellarg($file), $output, $return);

        if ($return === 0) {
            $stats = \explode(' ', \trim((string) \preg_replace('`[ ]+`', ' ', $string)));
            $size  = (int) $stats[1];
        } else {
            //~ Estimation size
            $stats = \stat($file);
            $size  = ($stats['size'] ?? 0) * 5;
        }

        return $size;
    }

    /**
     * Enable / Disable specified flag.
     */
    protected function enableFlag(int $flag, bool $enable = true): static
    {
        $flags = $this->getFlags();

        if ($enable) {
            $flags = ($flags | $flag);
        } else {
            $flags = ($flags - ($flags & $flag));
        }

        $this->setFlags($flags);

        return $this;
    }

    /**
     * Enable / Disable skip empty lines when parse file.
     */
    public function skipEmptyLines(bool $skipEmptyLines = true): static
    {
        return $this->enableFlag(self::SKIP_EMPTY, $skipEmptyLines);
    }

    /**
     * Enable / Disable drop new lines at the end of a line.
     */
    public function dropNewLines(bool $dropNewLines = true): static
    {
        return $this->enableFlag(self::DROP_NEW_LINE, $dropNewLines);
    }

    /**
     * Enable / Disable read lines as CSV rows.
     */
    public function readCsv(bool $readCsv = true): static
    {
        return $this->enableFlag(self::READ_CSV, $readCsv);
    }

    /**
     * Enable / Disable read on rewind/next.
     */
    public function readAhead(bool $readAhead = true): static
    {
        return $this->enableFlag(self::READ_AHEAD, $readAhead);
    }

    /**
     * @param bool $removeBOM
     */
    public function removeBOM(bool $removeBOM = true): void
    {
        $this->removeBOM = $removeBOM;
    }

    /**
     * @throws \RuntimeException
     */
    public function remove(): bool
    {
        if ($this->isDir() && $this->getPathname() === '..') {
            throw new \RuntimeException(__METHOD__ . '|Cannot remove parent directory !', 10001);
        }

        if ($this->isDir()) {
            $removed = \rmdir($this->getPath());
        } else {
            $removed = \unlink($this->getPathname());
        }

        if (!$removed) {
            throw new \RuntimeException(__METHOD__ . '|Cannot remove file / directory !', 10002);
        }

        return true;
    }

    /**
     * @throws \RuntimeException
     */
    public static function chmod(string $file, int $mode = 0644): void
    {
        if (!\file_exists($file)) {
            throw new \RuntimeException("File does not exist! (file: $file)");
        }

        $ownerId   = \fileowner($file);
        $currentId = \posix_getuid();
        if ($ownerId === false || $currentId !== $ownerId) {
            throw new Exception\FilePermissionException(
                "chmod can only be run by root or the owner! (file: '$file', owner_id: $ownerId, current_id: $currentId)"
            );
        }

        if (!\chmod($file, $mode)) {
            throw new Exception\FilePermissionException('Cannot change mode for current file! (file: ' . $file . ')');
        }
    }

    /**
     * @throws \RuntimeException
     */
    public static function chgrp(string $file, string $group = 'users'): void
    {
        if (!\file_exists($file)) {
            throw new \RuntimeException("File does not exist! (file: $file)");
        }

        if (!\chgrp($file, $group)) {
            throw new \RuntimeException("annot change group for current file! (file: $file)");
        }
    }

    /**
     * @return int
     */
    public function countLines(): int
    {
        $this->seek(PHP_INT_MAX);
        $lines = $this->key();
        $this->rewind();

        return $lines;
    }

    /**
     * @return list<string>|string|false
     */
    public function current(): string|array|false
    {
        if (!$this->removeBOM || $this->key() > 0) {
            return parent::current();
        }

        $line = parent::current();
        if (\is_array($line) && !empty($line)) {
            $line[0] = \str_replace(static::BOM_CHARACTER, '', $line[0]);
        } elseif (is_string($line)) {
            $line = \str_replace(static::BOM_CHARACTER, '', $line);
        }

        return $line;
    }
}
