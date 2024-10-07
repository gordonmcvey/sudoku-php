<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\traits\SubGridHelper;
use JsonSerializable;
use TypeError;

/**
 * Grid state class
 *
 * We can't actually make this class or its properties read-only, so this class just doesn't expose any methods that
 * will mutate the class state.
 */
class Grid implements GridContract, JsonSerializable
{
    use SubGridHelper;

    private const int MIN_CELL_VALUE = 1;
    private const int MAX_CELL_VALUE = 9;

    private const int TOTAL_ROWS = 9;
    private const int TOTAL_COLUMNS = 9;

    /**
     * @param array<int, array<int, int>> $gridState Puzzle to solve
     * @throws InvalidGridCoordsException If any cell co-ordinates don't fall within the grid
     * @throws CellValueRangeException If any cell has a value that isn't 1 - 9
     * @throws TypeError If any grid keys or values are invalid types
     */
    public function __construct(
        protected array $gridState = [],
    ) {
        $this->assertGrid($gridState);
    }

    public function grid(): array
    {
        return $this->gridState;
    }

    public function cellAtCoordinates(int $row, int $column): ?int
    {
        $this->assertRowIdIsInRange($row);
        $this->assertColumnIdIsInRange($column);

        return $this->gridState[$row][$column] ?? null;
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function jsonSerialize(): array
    {
        return $this->gridState;
    }

    /**
     * @throws InvalidGridCoordsException if the column is not in the allowed range
     */
    protected function assertRowIdIsInRange(int $rowId): void
    {
        if ($rowId < 0 || $rowId > self::TOTAL_ROWS - 1) {
            throw new InvalidGridCoordsException(sprintf(
                "Row ID %d is outside of the valid grid range %d - %d",
                $rowId,
                0,
                self::TOTAL_ROWS,
            ));
        }
    }

    /**
     * @throws InvalidGridCoordsException if the column is not in the allowed range
     */
    protected function assertColumnIdIsInRange(int $columnId): void
    {
        if ($columnId < 0 || $columnId > self::TOTAL_COLUMNS - 1) {
            throw new InvalidGridCoordsException(sprintf(
                "Column ID %d is outside of the valid grid range %d - %d",
                $columnId,
                0,
                self::TOTAL_COLUMNS,
            ));
        }
    }

    /**
     * @throws CellValueRangeException if the value is not in the allowed range
     */
    protected function assertCellValueInRange(int $value): void
    {
        if ($value < self::MIN_CELL_VALUE || $value > self::MAX_CELL_VALUE) {
            throw new CellValueRangeException(sprintf(
                "Cell value %d not in the range of %d - %d",
                $value,
                self::MIN_CELL_VALUE,
                self::MAX_CELL_VALUE,
            ));
        }
    }

    /**
     * Validate that the specified row contains unique values
     *
     * @param array<int, int> $row
     * @throws InvalidRowUniqueConstraintException
     */
    protected function assertUniqueRow(array $row): void
    {
        if (!$this->groupIsUnique($row)) {
            throw new InvalidRowUniqueConstraintException("Invalid grid: duplicate values in row");
        }
    }

    /**
     * Validate that the specified column contains unique values
     *
     * @param array<int, int> $column
     * @throws InvalidColumnUniqueConstraintException
     */
    protected function assertUniqueColumn(array $column): void
    {
        if (!$this->groupIsUnique($column)) {
            throw new InvalidColumnUniqueConstraintException("Invalid grid: duplicate values in column");
        }
    }

    /**
     * Validate that the specified subgrid contains unique values
     *
     * @param array<int, int> $subGrid
     */
    protected function assertUniqueSubGrid(array $subGrid): void
    {
        if (!$this->groupIsUnique($subGrid)) {
            throw new InvalidSubGridUniqueConstraintException("Invalid grid: duplicate values in subgrid");
        }
    }

    /**
     * @param array<int, array<int, int>> $grid
     * @return array<int>
     */
    protected function subGridValues(array $grid, int $subGridId): array
    {
        $subGridKeys = $this->cellIdsForSubGrid($subGridId);
        $subGrid = [];

        foreach ($subGridKeys as $rowId => $columnIds) {
            foreach ($columnIds as $columnId) {
                !isset($grid[$rowId][$columnId]) || $subGrid[] = $grid[$rowId][$columnId];
            }
        }

        return array_filter($subGrid);
    }

    /**
     * @param array<int, array<int, int>> $grid
     * @throws InvalidGridCoordsException if any grid coordinates are out of range
     * @throws CellValueRangeException if any cell values are out of range
     * @throws TypeError if any array keys or values aren't of the required type
     */
    private function assertGrid(array $grid): void
    {
        foreach ($grid as $rowId => $row) {
            $this->assertKeyType($rowId);
            $this->assertRowIdIsInRange($rowId);
            foreach ($row as $columnId => $cellValue) {
                $this->assertKeyType($columnId);
                $this->assertValueType($cellValue);
                $this->assertColumnIdIsInRange($columnId);
                $this->assertCellValueInRange($cellValue);
            }
        }
        $this->assertUniqueRows($grid);
        $this->assertUniqueColumns($grid);
        $this->assertUniqueSubGrids($grid);
    }

    /**
     * @throws TypeError if the key isn't an integer
     */
    private function assertKeyType(mixed $key): void
    {
        if (!is_int($key)) {
            throw new TypeError("Grid references must be an integer");
        }
    }

    /**
     * @throws TypeError if the value isn't an integer
     */
    private function assertValueType(mixed $value): void
    {
        if (!is_int($value)) {
            throw new TypeError("Cell entries must be an integer");
        }
    }

    /**
     * @param array<int, array<int, int>> $grid
     * @throws InvalidRowUniqueConstraintException
     */
    private function assertUniqueRows(array $grid): void
    {
        foreach ($grid as $row) {
            $this->assertUniqueRow($row);
        }
    }

    /**
     * @param array<int, array<int, int>> $grid
     * @throws InvalidColumnUniqueConstraintException
     */
    private function assertUniqueColumns(array $grid): void
    {
        for ($columnId = 0; $columnId < self::TOTAL_COLUMNS; $columnId++) {
            $this->assertUniqueColumn(array_column($grid, $columnId));
        }
    }

    /**
     * @param array<int, array<int, int>> $grid
     */
    private function assertUniqueSubGrids(array $grid): void
    {
        foreach (self::SUBGRID_IDS as $subGridId) {
            $subGrid = $this->subGridValues($grid, $subGridId);
            $this->assertUniqueSubGrid($subGrid);
        }
    }

    /**
     * @param array<int, int> $group
     */
    private function groupIsUnique(array $group): bool
    {
        $found = [];

        foreach ($group as $value) {
            if (isset($found[$value])) {
                return false;
            }
            $found[$value] = true;
        }

        return true;
    }
}
