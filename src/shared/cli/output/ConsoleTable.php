<?php
namespace PharIo\Phive\Cli;

class ConsoleTable {

    const COLUMN_PADDING = 4;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @param array $headers
     */
    public function __construct(array $headers) {
        $this->headers = $headers;
    }

    /**
     * @param array $row
     */
    public function addRow(array $row) {
        $this->rows[] = $row;
    }

    /**
     * @return string
     */
    public function asString() {
        $output = '';
        $mask = '';
        $totalWidth = 0;
        foreach ($this->headers as $index => $header) {
            $columnWidth = $this->getColWidth($index);
            $totalWidth += $columnWidth;
            $mask .= "%-{$columnWidth}.{$columnWidth}s";
        }
        $mask .= "\n";

        $output .= vsprintf($mask, $this->headers) . "\n";

        foreach ($this->rows as $row) {
            $output .= vsprintf($mask, $row);
        }

        return $output;
    }

    /**
     * @param int $index
     *
     * @return int
     */
    private function getColWidth($index) {
        $colWidth = strlen($this->headers[$index]);
        foreach ($this->rows as $row) {
            $colWidth = max($colWidth, strlen($row[$index]));
        }
        return $colWidth + self::COLUMN_PADDING;
    }
}