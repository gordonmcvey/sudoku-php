<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\interface\GridContract;
use JsonSerializable;
use TypeError;
use ValueError;

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

    private const int TOTAL_ROWS = 9;
    private const int TOTAL_COLUMNS = 9;

    private const int TOP_LEFT = 0;
    private const int TOP_CENTRE = 1;
    private const int TOP_RIGHT = 2;
    private const int CENTRE_LEFT = 3;
    private const int CENTRE_CENTRE = 4;
    private const int CENTRE_RIGHT = 5;
    private const int BOTTOM_LEFT = 6;
    private const int BOTTOM_CENTRE = 7;
    private const int BOTTOM_RIGHT = 8;

    private const array SUBGRID_IDS = [
        self::TOP_LEFT,
        self::TOP_CENTRE,
        self::TOP_RIGHT,
        self::CENTRE_LEFT,
        self::CENTRE_CENTRE,
        self::CENTRE_RIGHT,
        self::BOTTOM_LEFT,
        self::BOTTOM_CENTRE,
        self::BOTTOM_RIGHT,
    ];

    private const array SUBGRID_CELLS = [
        self::TOP_LEFT      => [
            0 => [0, 1, 2],
            1 => [0, 1, 2],
            2 => [0, 1, 2],
        ],
        self::TOP_CENTRE    => [
            0 => [3, 4, 5],
            1 => [3, 4, 5],
            2 => [3, 4, 5],
        ],
        self::TOP_RIGHT     => [
            0 => [6, 7, 8],
            1 => [6, 7, 8],
            2 => [6, 7, 8],
        ],
        self::CENTRE_LEFT   => [
            3 => [0, 1, 2],
            4 => [0, 1, 2],
            5 => [0, 1, 2],
        ],
        self::CENTRE_CENTRE => [
            3 => [3, 4, 5],
            4 => [3, 4, 5],
            5 => [3, 4, 5],
        ],
        self::CENTRE_RIGHT  => [
            3 => [6, 7, 8],
            4 => [6, 7, 8],
            5 => [6, 7, 8],
        ],
        self::BOTTOM_LEFT   => [
            6 => [0, 1, 2],
            7 => [0, 1, 2],
            8 => [0, 1, 2],
        ],
        self::BOTTOM_CENTRE => [
            6 => [3, 4, 5],
            7 => [3, 4, 5],
            8 => [3, 4, 5],
        ],
        self::BOTTOM_RIGHT  => [
            6 => [6, 7, 8],
            7 => [6, 7, 8],
            8 => [6, 7, 8],
        ],
    ];

    private const array CELL_SUBGRID_MAP = [
        0 => [
            0 => self::TOP_LEFT,
            1 => self::TOP_LEFT,
            2 => self::TOP_LEFT,
            3 => self::TOP_CENTRE,
            4 => self::TOP_CENTRE,
            5 => self::TOP_CENTRE,
            6 => self::TOP_RIGHT,
            7 => self::TOP_RIGHT,
            8 => self::TOP_RIGHT,
        ],
        1 => [
            0 => self::TOP_LEFT,
            1 => self::TOP_LEFT,
            2 => self::TOP_LEFT,
            3 => self::TOP_CENTRE,
            4 => self::TOP_CENTRE,
            5 => self::TOP_CENTRE,
            6 => self::TOP_RIGHT,
            7 => self::TOP_RIGHT,
            8 => self::TOP_RIGHT,
        ],
        2 => [
            0 => self::TOP_LEFT,
            1 => self::TOP_LEFT,
            2 => self::TOP_LEFT,
            3 => self::TOP_CENTRE,
            4 => self::TOP_CENTRE,
            5 => self::TOP_CENTRE,
            6 => self::TOP_RIGHT,
            7 => self::TOP_RIGHT,
            8 => self::TOP_RIGHT,
        ],
        3 => [
            0 => self::CENTRE_LEFT,
            1 => self::CENTRE_LEFT,
            2 => self::CENTRE_LEFT,
            3 => self::CENTRE_CENTRE,
            4 => self::CENTRE_CENTRE,
            5 => self::CENTRE_CENTRE,
            6 => self::CENTRE_RIGHT,
            7 => self::CENTRE_RIGHT,
            8 => self::CENTRE_RIGHT,
        ],
        4 => [
            0 => self::CENTRE_LEFT,
            1 => self::CENTRE_LEFT,
            2 => self::CENTRE_LEFT,
            3 => self::CENTRE_CENTRE,
            4 => self::CENTRE_CENTRE,
            5 => self::CENTRE_CENTRE,
            6 => self::CENTRE_RIGHT,
            7 => self::CENTRE_RIGHT,
            8 => self::CENTRE_RIGHT,
        ],
        5 => [
            0 => self::CENTRE_LEFT,
            1 => self::CENTRE_LEFT,
            2 => self::CENTRE_LEFT,
            3 => self::CENTRE_CENTRE,
            4 => self::CENTRE_CENTRE,
            5 => self::CENTRE_CENTRE,
            6 => self::CENTRE_RIGHT,
            7 => self::CENTRE_RIGHT,
            8 => self::CENTRE_RIGHT,
        ],
        6 => [
            0 => self::BOTTOM_LEFT,
            1 => self::BOTTOM_LEFT,
            2 => self::BOTTOM_LEFT,
            3 => self::BOTTOM_CENTRE,
            4 => self::BOTTOM_CENTRE,
            5 => self::BOTTOM_CENTRE,
            6 => self::BOTTOM_RIGHT,
            7 => self::BOTTOM_RIGHT,
            8 => self::BOTTOM_RIGHT,
        ],
        7 => [
            0 => self::BOTTOM_LEFT,
            1 => self::BOTTOM_LEFT,
            2 => self::BOTTOM_LEFT,
            3 => self::BOTTOM_CENTRE,
            4 => self::BOTTOM_CENTRE,
            5 => self::BOTTOM_CENTRE,
            6 => self::BOTTOM_RIGHT,
            7 => self::BOTTOM_RIGHT,
            8 => self::BOTTOM_RIGHT,
        ],
        8 => [
            0 => self::BOTTOM_LEFT,
            1 => self::BOTTOM_LEFT,
            2 => self::BOTTOM_LEFT,
            3 => self::BOTTOM_CENTRE,
            4 => self::BOTTOM_CENTRE,
            5 => self::BOTTOM_CENTRE,
            6 => self::BOTTOM_RIGHT,
            7 => self::BOTTOM_RIGHT,
            8 => self::BOTTOM_RIGHT,
        ],
    ];

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

    protected function coordinatesToSubgridId(int $row, int $column): int
    {
        return self::CELL_SUBGRID_MAP[$row][$column] ?? throw new ValueError("Invalid coordinates: $row, $column");
    }

    /**
     * @return array<int, array<int, int>>
     */
    protected function cellIdsForSubGrid(int $subGridId): array
    {
        return self::SUBGRID_CELLS[$subGridId] ?? throw new ValueError("Invalid subgrid ID: $subGridId");
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
