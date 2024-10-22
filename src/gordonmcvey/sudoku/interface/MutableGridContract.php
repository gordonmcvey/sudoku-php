<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\interface;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;

interface MutableGridContract extends GridContract
{
    public function fillCoordinates(RowIds $row, ColumnIds $column, int $value): self;

    public function clearCoordinates(RowIds $row, ColumnIds $column): self;
}
