<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use gordonmcvey\sudoku\enum\SubGridIds;
use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\util\SubGridMapper;
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
    private const int MIN_CELL_VALUE = 1;
    private const int MAX_CELL_VALUE = 9;

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

    public function row(RowIds $rowId): array
    {
        return array_values($this->gridState[$rowId->value] ?? []);
    }

    public function column(ColumnIds $columnId): array
    {
        return array_column($this->gridState, $columnId->value);
    }

    public function subGridAtCoordinates(RowIds $rowId, ColumnIds $columnId): array
    {
        return $this->subGrid(SubGridMapper::coordinatesToSubGridId($rowId, $columnId));
    }

    public function subGrid(SubGridIds $subGridId): array
    {
        return SubGridMapper::subGridValues($this->gridState, $subGridId);
    }

    public function cellAtCoordinates(RowIds $row, ColumnIds $column): ?int
    {
        return $this->gridState[$row->value][$column->value] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->gridState);
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function jsonSerialize(): array
    {
        return $this->grid();
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
            $this->assertRowId($rowId);
            foreach ($row as $columnId => $cellValue) {
                $this->assertColumnId($columnId);
                $this->assertCellValueType($cellValue);
                $this->assertCellValueInRange($cellValue);
            }
        }
        $this->assertUniqueRows($grid);
        $this->assertUniqueColumns($grid);
        $this->assertUniqueSubGrids($grid);
    }

    /**
     * @throws InvalidGridCoordsException if the column is not in the allowed range
     */
    protected function assertRowId(int $rowId): void
    {
        if (!RowIds::tryFrom($rowId)) {
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
    protected function assertColumnId(int $columnId): void
    {
        if (!ColumnIds::tryFrom($columnId)) {
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
     * @throws TypeError if the value isn't an integer
     */
    private function assertCellValueType(mixed $value): void
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
        foreach (SubGridIds::cases() as $subGridId) {
            $subGrid = SubGridMapper::subGridValues($grid, $subGridId);
            $this->assertUniqueSubGrid($subGrid);
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
