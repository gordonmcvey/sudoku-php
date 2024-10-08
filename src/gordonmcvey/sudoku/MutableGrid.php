<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\ImmutableCellException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\interface\MutableGridContract;
use JsonSerializable;

class MutableGrid extends Grid implements GridContract, MutableGridContract, JsonSerializable
{
    /**
     * @throws CellValueRangeException if the given value is not in the valid range
     * @throws InvalidGridCoordsException if the coordinates are not in the valid range
     * @throws ImmutableCellException if the coordinates refer to a cell defined in the puzzle
     */
    public function fillCoordinates(int $row, int $column, int $value): self
    {
        $this->assertCellValueInRange($value);
        $this->assertRowIdIsInRange($row);
        $this->assertColumnIdIsInRange($column);

        $grid = $this->gridState;
        $grid[$row][$column] = $value;
        ksort($grid[$row]);
        ksort($grid);

        $this->assertUniqueRow($grid[$row]);
        $this->assertUniqueColumn(array_column($grid, $column));
        $this->assertUniqueSubGrid(
            $this->subGridValues($grid, $this->coordinatesToSubgridId($row, $column))
        );

        $this->gridState = $grid;

        return $this;
    }
}
