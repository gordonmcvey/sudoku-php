<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\interface;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use gordonmcvey\sudoku\enum\SubGridIds;

interface GridContract
{
    public const int TOTAL_ROWS = 9;
    public const int TOTAL_COLUMNS = 9;

    /**
     * Get the grid data as an array
     *
     * @return array<int, array<int, int>>
     */
    public function grid(): array;

    /**
     * @return array<int, int>
     */
    public function row(RowIds $rowId): array;

    /**
     * @return array<int, int>
     */
    public function column(ColumnIds $columnId): array;

    /**
     * @return array<int>
     */
    public function subGridAtCoordinates(RowIds $rowId, ColumnIds $columnId): array;

    /**
     * @return array<int>
     */
    public function subGrid(SubGridIds $subGridId): array;

    /**
     * Return the value of the cell specified by the given coordinates
     */
    public function cellAtCoordinates(RowIds $row, ColumnIds $column): ?int;

    public function isEmpty(): bool;
}
