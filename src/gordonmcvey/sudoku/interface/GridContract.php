<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\interface;

interface GridContract
{
    /**
     * Get the grid data as an array
     *
     * @return array<int, array<int, int>>
     */
    public function grid(): array;

    /**
     * Return the value of the cell specified by the given coordinates
     */
    public function cellAtCoordinates(int $row, int $column): ?int;
}
