<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\interface;

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
    public function row(int $rowId): array;

    /**
     * @return array<int, int>
     */
    public function column(int $columnId): array;

    /**
     * @return array<int>
     */
    public function subGridAtCoordinates(int $rowId, int $columnId): array;

    /**
     * @return array<int>
     */
    public function subGrid(int $subGridId): array;

    /**
     * Return the value of the cell specified by the given coordinates
     */
    public function cellAtCoordinates(int $row, int $column): ?int;

    public function isEmpty(): bool;
}
