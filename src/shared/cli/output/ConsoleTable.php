<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace PharIo\Phive\Cli;

use function max;
use function strlen;
use function vsprintf;

class ConsoleTable {
    public const COLUMN_PADDING = 4;

    /** @var array */
    private $headers;

    /** @var array */
    private $rows = [];

    public function __construct(array $headers) {
        $this->headers = $headers;
    }

    public function addRow(array $row): void {
        $this->rows[] = $row;
    }

    public function asString(): string {
        $output = '';
        $mask   = '';

        foreach ($this->headers as $index => $header) {
            $columnWidth = $this->getColWidth($index);
            $mask .= "%-{$columnWidth}.{$columnWidth}s";
        }
        $mask .= "\n";

        $output .= vsprintf($mask, $this->headers) . "\n";

        foreach ($this->rows as $row) {
            $output .= vsprintf($mask, $row);
        }

        return $output;
    }

    private function getColWidth(int $index): int {
        $colWidth = strlen($this->headers[$index]);

        foreach ($this->rows as $row) {
            $colWidth = max($colWidth, strlen($row[$index]));
        }

        return $colWidth + self::COLUMN_PADDING;
    }
}
