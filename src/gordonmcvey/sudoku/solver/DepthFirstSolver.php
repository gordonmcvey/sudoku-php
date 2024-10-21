<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\solver;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use gordonmcvey\sudoku\Grid;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\interface\MutableGridContract;
use gordonmcvey\sudoku\util\SubGridMapper;

/**
 * Depth-first implementation of the Solver interface
 *
 * This class implements a solving algorithm for Sudoku problems based on the depth-first method with backtracking.
 * The pros of this approach are that it's relatively simple (once you've wrapped your head around the recursion) and
 * is guaranteed to find a solution for any solvable Sudoku problem.
 *
 * However, this is essentially a brute-force approach with O(9^n) time complexity in the worst case (9 options per
 * blank cell ^ n blank cells).  It's possible to design a puzzle that is valid and solvable, but which will trigger
 * pathological behaviour resulting in the algorithm running for several minutes to find the solution.
 *
 * This is therefore intended to be a fallback solver, the plan is to implement more intelligent solvers that can
 * overcome or mitigate the limitations of this approach.
 *
 * @link https://www.youtube.com/watch?v=eAFcj_2quWI The implementation is loosely based on the one demonstrated here
 */
class DepthFirstSolver
{
    /**
     * @var array<int, array<int, int>>
     */
    private array $gridState;

    public function __construct(private MutableGridContract $grid)
    {
    }

    public function solve(): ?GridContract
    {
        $this->gridState = $this->grid->grid();

        return $this->findSolution() ?
            $this->buildSolution() :
            null;
    }

    private function buildSolution(): GridContract
    {
        ksort($this->gridState);
        foreach ($this->gridState as &$row) {
            ksort($row);
        }

        return new Grid($this->gridState);
    }

    private function findSolution(
        int $rowKey = RowIds::ROW_1->value,
        int $columnKey = ColumnIds::COL_1->value,
    ): bool {
        $rowId = RowIds::tryFrom($rowKey);
        $columnId = ColumnIds::tryFrom($columnKey);

        if (!$rowId) {
            // If we've passed the end of the grid then we've succeeded in finding a solution
            return true;
        } elseif (!$columnId) {
            // If we've exceeded the end of this row then move to the next one
            return $this->findSolution($rowKey + 1);
        } elseif (isset($this->gridState[$rowKey][$columnKey])) {
            // If this cell already has a value, move on to the next one
            return $this->findSolution($rowKey, $columnKey + 1);
        } else {
            // Find a valid solution for this cell
            // @todo Refactoring to allow the use of the OptionFinder
            $options = array_values(array_diff(
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                $this->gridState[$rowKey] ?? [],
                array_column($this->gridState, $columnKey),
                SubGridMapper::subGridValues(
                    $this->gridState,
                    SubGridMapper::coordinatesToSubGridId($rowId, $columnId)
                ),
            ));

            foreach ($options as $option) {
                // Try each possible value in this cell then attempt to solve the rest of the puzzle
                $this->gridState[$rowKey][$columnKey] = $option;
                if ($this->findSolution($rowKey, $columnKey + 1)) {
                    return true;
                } else {
                    // If the rest of the puzzle can't be solved with the current value in this cell, clear it so we can
                    // try the next option (if any)
                    unset($this->gridState[$rowKey][$columnKey]);
                }
            }

            // If we got here then we failed to solve the puzzle on this branch, either we'll have to backtrack and try
            // another option, or there are no more options and the puzzle is not solvable
            return false;
        }
    }
}
