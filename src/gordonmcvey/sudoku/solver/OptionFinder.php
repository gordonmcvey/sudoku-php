<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\solver;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
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

    public function __construct(private readonly GridContract $grid)
    {
    }

    /**
     * Find the possible options for the unfilled cells in the given grid
     *
     * For each cell in the given grid, the available options are 1 - 9, excluding any of those values that appear in
     * the same row, column, or subgrid.
     *
     * The returned array only contains options for the unfilled cells.  If a cell is filled then it by definition has
     * no optional values to fill for that cell.
     *
     * @return array<int, array<int, array<int>>> Options for each cell, keyed by row and column
     */
    public function findOptionsFor(): array
    {
        // Early out to skip unnecessary filtering when there's nothing to filter
        if ($this->grid->isEmpty()) {
            return $this->emptyGridOptions();
        }

        $gridOptions = [];

        foreach (RowIds::cases() as $rowId) {
            foreach (ColumnIds::cases() as $columnId) {
                $options = $this->findOptionsForCell($rowId, $columnId);
                empty($options) || $gridOptions[$rowId->value][$columnId->value] = $options;
            }
        }

        return $gridOptions;
    }

    /**
     * Find all the options that could be valid for the specified cell in the given grid
     *
     * @return array<int> List of possible options. Empty if the cell is filled or if there are no valid options
     */
    public function findOptionsForCell(RowIds $rowId, ColumnIds $columnId): array
    {
        $cellValue = $this->grid->cellAtCoordinates($rowId, $columnId);
        return null === $cellValue ?
            array_values(array_diff(
                self::ALL_OPTIONS,
                $this->grid->row($rowId),
                $this->getColumn($columnId),
                $this->getSubGrid($rowId, $columnId),
            )) :
            [];
    }

    /**
     * @return array<int>
     */
    private function getColumn(ColumnIds $columnId): array
    {
        $columnKey = $columnId->value;
        isset($this->columnCache[$columnKey]) || $this->columnCache[$columnKey] = $this->grid->column($columnId);

        return $this->columnCache[$columnKey];
    }

    /**
     * @return array<int>
     */
    private function getSubGrid(RowIds $rowId, ColumnIds $columnId): array
    {
        $subGridId = SubGridMapper::coordinatesToSubGridId($rowId, $columnId);
        $subGridKey = $subGridId->value;

        isset($this->subGridCache[$subGridKey]) || $this->subGridCache[$subGridKey] = $this->grid->subGrid($subGridId);

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
