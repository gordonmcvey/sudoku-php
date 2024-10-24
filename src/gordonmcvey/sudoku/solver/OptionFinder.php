<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\solver;

use gordonmcvey\sudoku\dto\CellOptions;
use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use gordonmcvey\sudoku\enum\SubGridIds;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\util\SubGridMapper;

/**
 * OptionFinder
 *
 * This class finds all the potential solutions to the blank slots in a given Sudoku grid.  This can be used to give
 * hints to the player, allow automated solvers to direct their searches more effectively, etc.
 */
readonly class OptionFinder
{
    private const array ALL_OPTIONS = [1, 2, 3, 4, 5, 6, 7, 8, 9];

    /**
     * Find the possible options for the unfilled cells in the given grid
     *
     * For each cell in the given grid, the available options are 1 - 9, excluding any of those values that appear in
     * the same row, column, or subgrid.
     *
     * The returned array only contains options for the unfilled cells.  If a cell is filled then it by definition has
     * no optional values to fill for that cell.
     *
     * @return array<int, array<int, CellOptions>> Options for each cell, keyed by row and column
     */
    public function findOptionsFor(GridContract $grid): array
    {
        // Early out to skip unnecessary filtering when there's nothing to filter
        if ($grid->isEmpty()) {
            return $this->emptyGridOptions();
        }

        $gridCache = $grid->grid();
        $columnCache = [];
        $subGridCache = [];
        $gridOptions = [];

        // Rows are only iterated once, but columns and subgrids are iterated multiple times
        foreach (ColumnIds::cases() as $columnId) {
            $columnCache[$columnId->value] = $grid->column($columnId);
        }

        foreach (SubGridIds::cases() as $subGridId) {
            $subGridCache[$subGridId->value] = $grid->subGrid($subGridId);
        }

        foreach (RowIds::cases() as $rowId) {
            foreach (ColumnIds::cases() as $columnId) {
                $options = !isset($gridCache[$rowId->value][$columnId->value]) ?
                    array_values(array_diff(
                        self::ALL_OPTIONS,
                        $gridCache[$rowId->value] ?? [],
                        $columnCache[$columnId->value],
                        $subGridCache[SubGridMapper::coordinatesToSubGridId($rowId, $columnId)->value],
                    )) :
                    [];
                empty($options)
                    || $gridOptions[$rowId->value][$columnId->value] = new CellOptions($rowId, $columnId, $options);
            }
        }

        return $gridOptions;
    }

    /**
     * Find all the options that could be valid for the specified cell in the given grid
     *
     * @return CellOptions List of possible options. Empty if the cell is filled or if there are no valid options
     */
    public function findOptionsForCell(GridContract $grid, RowIds $rowId, ColumnIds $columnId): CellOptions
    {
        return new CellOptions(
            $rowId,
            $columnId,
            null === $grid->cellAtCoordinates($rowId, $columnId) ?
            array_values(array_diff(
                self::ALL_OPTIONS,
                $grid->row($rowId),
                $grid->column($columnId),
                $grid->subGridAtCoordinates($rowId, $columnId),
            )) :
            []
        );
    }

    /**
     * @return array<int, array<int, CellOptions>>
     */
    private function emptyGridOptions(): array
    {
        $gridOptoons = [];

        foreach (RowIds::cases() as $rowId) {
            foreach (ColumnIds::cases() as $columnId) {
                $gridOptions[$rowId->value][$columnId->value] = new CellOptions($rowId, $columnId, self::ALL_OPTIONS);
            }
        }

        return $gridOptions;
    }
}
