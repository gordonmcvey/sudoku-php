<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\ImmutableCellException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use gordonmcvey\sudoku\interface\MutableGridContract;
use gordonmcvey\sudoku\util\SubGridMapper;
use gordonmcvey\sudoku\util\UniqueGridValidator;
use JsonSerializable;

class MutableGrid extends Grid implements MutableGridContract, JsonSerializable
{
    use UniqueGridValidator;

    /**
     * @throws CellValueRangeException if the given value is not in the valid range
     * @throws InvalidGridCoordsException if the coordinates are not in the valid range
     * @throws ImmutableCellException if the coordinates refer to a cell defined in the puzzle
     */
    public function fillCoordinates(RowIds $row, ColumnIds $column, int $value): self
    {
        $this->assertCellValueInRange($value);
        $rowKey = $row->value;
        $columnKey = $column->value;

        $grid = $this->gridState;
        $grid[$rowKey][$columnKey] = $value;
        ksort($grid[$rowKey]);
        ksort($grid);

        $this->assertUniqueRow($grid[$rowKey]);
        $this->assertUniqueColumn(array_column($grid, $columnKey));
        $this->assertUniqueSubGrid(
            SubGridMapper::subGridValues($grid, SubGridMapper::coordinatesToSubgridId($row, $column))
        );

        $this->gridState = $grid;

        return $this;
    }
}
