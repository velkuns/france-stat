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

/**
 * Class to manipulate files. Extends File (and SplFile*)
 *
 * @author Romain Cottard
 */
class FileCsv extends File
{
    public const COMMON_DELIMITERS = [',', ';', '|', "\t"];

    /**
     * @var array<string> $header Content for csv header file.
     */
    protected array $header = [];

    protected bool $skipHeader = false;

    /**
     * @param resource $context A valid context resource created with stream_context_create().
     */
    public function __construct(
        string $fileName,
        string $openMode = 'r',
        bool $useIncludePath = false,
        $context = null,
        bool $isGzCompressed = false,
    ) {
        parent::__construct($fileName, $openMode, $useIncludePath, $context, $isGzCompressed);

        $this->readAhead(true); // mandatory to skip empty line
        $this->skipEmptyLines(true);
        $this->dropNewLines(true);
        $this->readCsv(true);
    }

    /**
     * @param list<string> $moreDelimiters
     */
    public function autoDetectDelimiter(array $moreDelimiters = []): void
    {
        $columns    = [];
        $delimiters = \array_merge(self::COMMON_DELIMITERS, $moreDelimiters);
        $csvControl = $this->getCsvControl();

        $currentLine = $this->key();

        if ($currentLine > 0) {
            $this->seek(0);
            $header = $this->fgets();
            $this->seek($currentLine);
        } else {
            $header = $this->fgets();
            $this->seek(0);
        }

        foreach ($delimiters as $delimiter) {
            $columns[$delimiter] = \count(\str_getcsv((string) $header, $delimiter, $csvControl[1], $csvControl[2]));
        }

        // pick delimiter which yields the more columns
        $delimiter = \array_search(\max($columns), $columns);

        // update csv control
        $csvControl[0] = $delimiter;
        $this->setCsvControl(...$csvControl);
    }

    public function skipHeader(bool $skipHeader = true): static
    {
        $this->skipHeader = $skipHeader;

        //~ Get Header content.
        $this->getHeader();

        //~ Go to the next line if we are on the first line.
        if ($this->key() === 0 && $this->skipHeader) {
            $this->next();
        }

        return $this;
    }

    /**
     * @return array<string> Header line data
     */
    public function getHeader(): array
    {
        if (!empty($this->header)) {
            return $this->header;
        }

        //~ Store current position
        $currentLine = $this->key();

        //~ Move to line only if current line is not first (optimization)
        if ($currentLine > 0) {
            $this->seek(0); // Go to the first line
            $header = $this->current(); // Get Header content (current first line)
            $this->seek($currentLine); // Go to the original position in file.
        } else {
            $header = $this->current();
        }

        if (is_bool($header)) {
            $this->header = [];
        } elseif (is_string($header)) {
            $this->header = [$header];
        } else {
            $this->header = $header;
        }

        return $this->header;
    }

    /**
     * Rewinds the file back to the first line.
     * Force next line when skip header is enabled.
     *
     * @return void
     */
    public function rewind(): void
    {
        parent::rewind();

        if ($this->skipHeader) {
            $this->next();
        }
    }
}
