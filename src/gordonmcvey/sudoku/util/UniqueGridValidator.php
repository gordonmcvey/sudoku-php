<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\util;

use gordonmcvey\sudoku\enum\SubGridIds;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\interface\GridContract;

trait UniqueGridValidator
{
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
        for ($columnId = 0; $columnId < GridContract::TOTAL_COLUMNS; $columnId++) {
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
    private function assertUniqueRow(array $row): void
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
    private function assertUniqueColumn(array $column): void
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
    private function assertUniqueSubGrid(array $subGrid): void
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
