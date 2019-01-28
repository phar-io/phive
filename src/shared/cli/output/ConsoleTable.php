<?php declare(strict_types = 1);
namespace PharIo\Phive\Cli;

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
        $output     = '';
        $mask       = '';
        $totalWidth = 0;

        foreach ($this->headers as $index => $header) {
            $columnWidth = $this->getColWidth($index);
            $totalWidth += $columnWidth;
            $mask .= "%-{$columnWidth}.{$columnWidth}s";
        }
        $mask .= "\n";

        $output .= \vsprintf($mask, $this->headers) . "\n";

        foreach ($this->rows as $row) {
            $output .= \vsprintf($mask, $row);
        }

        return $output;
    }

    private function getColWidth(int $index): int {
        $colWidth = \strlen($this->headers[$index]);

        foreach ($this->rows as $row) {
            $colWidth = \max($colWidth, \strlen($row[$index]));
        }

        return $colWidth + self::COLUMN_PADDING;
    }
}
