<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\dto;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use JsonSerializable;

final readonly class CellOptions implements JsonSerializable
{
    /**
     * @param array<int> $options
     */
    public function __construct(
        public RowIds $rowId,
        public ColumnIds $columnId,
        public array $options,
    ) {
    }

    /**
     * @return array{
     *     row: int,
     *     column: int,
     *     options: array<int>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            "row"     => $this->rowId->value,
            "column"  => $this->columnId->value,
            "options" => $this->options,
        ];
    }
}
