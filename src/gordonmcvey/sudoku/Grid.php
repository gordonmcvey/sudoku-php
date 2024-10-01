<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\ImmutableCellException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use JsonSerializable;
use TypeError;

class Grid implements JsonSerializable
{
    private const int MIN_CELL_VALUE = 1;
    private const int MAX_CELL_VALUE = 9;

    private const int TOTAL_ROWS = 9;
    private const int TOTAL_COLUMNS = 9;

    /**
     * Cache of the combined puzzle and solution so it doesn't have to be computed every time it's needed
     *
     * @var array<int, array<int, int>>|null
     */
    private ?array $puzzleWithSolutionCache = null;

    /**
     * @param array<int, array<int, int>> $puzzle Puzzle to solve
     * @param array<int, array<int, int>> $solution Solution proposed by the player
     * @throws InvalidGridCoordsException If any cell co-ordinates are outside of the grid
     * @throws CellValueRangeException If any cell has a value that isn't 1 - 9
     * @throws ImmutableCellException If there's any overlap between the puzzle and the solution
     * @throws TypeError If any grid keys or values are invalid types
     */
    public function __construct(
        private readonly array $puzzle = [],
        private array          $solution = [],
    ) {
        $this->assertGrid($puzzle);
        $this->assertGrid($solution);
        $this->assertNoKeyOverlap($puzzle, $solution);
    }

    /**
     * Get the puzzle to solve
     *
     * @return array<int, array<int, int>>
     */
    public function puzzle(): array
    {
        return $this->puzzle;
    }

    /**
     * Get the solution entered by the player
     *
     * @return array<int, array<int, int>>
     */
    public function solution(): array
    {
        return $this->solution;
    }

    /**
     * Get the puzzle with the solution merged in
     *
     * @return array<int, array<int, int>>
     */
    public function puzzleWithSolution(): array
    {
        if (null === $this->puzzleWithSolutionCache) {
            $puzzle = $this->puzzle;

            foreach ($this->solution as $rowId => $solutionRow) {
                if (empty($puzzle[$rowId])) {
                    $puzzle[$rowId] = $solutionRow;
                    continue;
                }

                foreach ($solutionRow as $columnId => $cellValue) {
                    $puzzle[$rowId][$columnId] = $cellValue;
                }
                ksort($puzzle[$rowId]);
            }
            ksort($puzzle);

            $this->puzzleWithSolutionCache = $puzzle;
        }

        return $this->puzzleWithSolutionCache;
    }

    /**
     * Return the value of the cell specified by the given coordinates
     */
    public function cellAtCoordinates(int $row, int $column): ?int
    {
        $this->assertRowIdIsInRange($row);
        $this->assertColumnIdIsInRange($column);

        return $this->solution[$row][$column] ?? $this->puzzle[$row][$column] ?? null;
    }

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
        $this->assertCellIsMutable($row, $column);

        $this->solution[$row][$column] = $value;
        ksort($this->solution[$row]);
        ksort($this->solution);

        if (null !== $this->puzzleWithSolutionCache) {
            $this->puzzleWithSolutionCache[$row][$column] = $value;
            ksort($this->puzzleWithSolutionCache[$row]);
            ksort($this->puzzleWithSolutionCache);
        }

        return $this;
    }

    /**
     * @return array{
     *     puzzle: array<int, array<int, int>>,
     *     solution: array<int, array<int, int>>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            "puzzle"   => $this->puzzle,
            "solution" => $this->solution,
        ];
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
     * @throws InvalidGridCoordsException if the column is not in the allowed range
     */
    private function assertRowIdIsInRange(int $rowId): void
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
    private function assertColumnIdIsInRange(int $columnId): void
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
    private function assertCellValueInRange(int $value): void
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
     * @throws ImmutableCellException If the specified cell is part of the puzzle
     */
    private function assertCellIsMutable(int $row, int $column): void
    {
        if (isset($this->puzzle[$row][$column])) {
            throw new ImmutableCellException(
                "Can't add a solution entry at row $row, column $column, that cell is part of the puzzle"
            );
        }
    }

    /**
     * Validate that there are no entries in the solution that coincide with entries in the original puzzle
     *
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     * @throws ImmutableCellException if any of the cells in the solution conflict with the puzzle
     */
    private function assertNoKeyOverlap(array $puzzle, array $solution): void
    {
        foreach ($solution as $rowId => $columns) {
            $overlap = array_intersect(array_keys($columns), array_keys($puzzle[$rowId] ?? []));
            if (!empty($overlap)) {
                throw new ImmutableCellException("The solution contains entries that are part of the puzzle");
            }
        }
    }

    /**
     * Validate that the specified row contains unique values
     *
     * @todo Implement
     */
    private function assertUniqueRow(int $rowId): void
    {
    }

    /**
     * Validate that the specified column contains unique values
     *
     * @todo Implement
     */
    private function assertUniqueColumn(int $columnId): void
    {
    }

    /**
     * Validate that the specified subgrid contains unique values
     *
     * @todo Implement
     */
    private function assertUniqueSubgrid(): void
    {
    }
}
