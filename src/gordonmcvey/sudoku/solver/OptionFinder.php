<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\solver;

use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\util\SubGridMapper;

/**
 * OptionFinder
 *
 * This class finds all the potential solutions to the blank slots in a given Sudoku grid.  This can be used to give
 * hints to the player, allow automated solvers to direct their searches more effectively, etc.
 */
class OptionFinder
{
    private const array ALL_OPTIONS = [1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * @var array<int, array<int, int>>
     */
    private array $columnCache = [];

    /**
     * @var array<int, array<int, int>>
     */
    private array $subGridCache = [];

    /**
     * Find the possible options for the unfilled cells in the given grid
     *
     * For each cell in the given grid, the available options are 1 - 9, excluding any of those values that appear in
     * the same row, column, or subgrid.
     *
     * @param GridContract $currentState
     * @return array<int, array<int, array<int>>>
     */
    public function findOptionsFor(GridContract $currentState): array
    {
        // Early out to skip unnecessary filtering when there's nothing to filter
        if ($currentState->isEmpty()) {
            return $this->emptyGridOptions();
        }

        $gridOptions = [];

        for ($rowId = 0; $rowId < $currentState::TOTAL_ROWS; $rowId++) {
            $allocatedOnRow = $currentState->row($rowId);

            for ($columnId = 0; $columnId < $currentState::TOTAL_COLUMNS; $columnId++) {
                if (null === $currentState->cellAtCoordinates($rowId, $columnId)) {
                    $cellOptions = array_values(array_diff(
                        self::ALL_OPTIONS,
                        $allocatedOnRow,
                        $this->getColumn($currentState, $columnId),
                        $this->getSubGrid($currentState, $rowId, $columnId),
                    ));

                    $gridOptions[$rowId][$columnId] = $cellOptions;
                }
            }
        }

        return $gridOptions;
    }

    /**
     * @return array<int>
     */
    private function getColumn(GridContract $grid, int $columnId): array
    {
        isset($this->columnCache[$columnId]) || $this->columnCache[$columnId] = $grid->column($columnId);

        return $this->columnCache[$columnId];
    }

    /**
     * @return array<int>
     */
    private function getSubGrid(GridContract $grid, int $rowId, int $columnId): array
    {
        $subGridId = SubGridMapper::coordinatesToSubGridId($rowId, $columnId);
        $subGridKey = $subGridId->value;

        isset($this->subGridCache[$subGridKey]) || $this->subGridCache[$subGridKey] = $grid->subGrid($subGridId);

        return $this->subGridCache[$subGridKey];
    }

    /**
     * @return array<int, array<int, array<int>>>
     */
    private function emptyGridOptions(): array
    {
        return array_fill(
            0,
            GridContract::TOTAL_ROWS,
            array_fill(
                0,
                GridContract::TOTAL_COLUMNS,
                self::ALL_OPTIONS
            )
        );
    }
}
