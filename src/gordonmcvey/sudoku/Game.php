<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

use gordonmcvey\sudoku\exception\ImmutableCellException;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\interface\MutableGridContract;

class Game implements \JsonSerializable
{
    /**
     * @var array<int, array<int, int>>|null
     */
    private ?array $gameCache = null;

    public function __construct(
        private readonly GridContract $puzzle,
        private readonly MutableGridContract $solution,
    ) {
        $this->assertSolutionDoesntOverlapPuzzle($puzzle->grid(), $solution->grid());
    }

    public function puzzleWithSolution(): Grid
    {
        if (null === $this->gameCache) {
            $this->gameCache = $this->rebuildCache();
        }

        return new Grid($this->gameCache);
    }

    /**
     * @return array<int, array<int, int>>
     */
    private function rebuildCache(): array
    {
        $puzzleWithSolution = $this->puzzle->grid();

        foreach ($this->solution->grid() as $rowId => $solutionRow) {
            if (empty($puzzleWithSolution[$rowId])) {
                $puzzleWithSolution[$rowId] = $solutionRow;
                continue;
            }

            foreach ($solutionRow as $columnId => $cellValue) {
                $puzzleWithSolution[$rowId][$columnId] = $cellValue;
            }
            ksort($puzzleWithSolution[$rowId]);
        }
        ksort($puzzleWithSolution);

        return $puzzleWithSolution;
    }

    public function fillCoordinates(int $row, int $column, int $value): self
    {
        // You can't insert any values into the solution that are already in the puzzle
        if ($this->puzzle->cellAtCoordinates($row, $column)) {
            throw new ImmutableCellException(
                "Cell at coordinates [$row, $column] is in the puzzle, cannot insert into the solution"
            );
        }

        // @todo This approach works, but could be better
        $currentState = new MutableGrid($this->rebuildCache());
        $currentState->fillCoordinates($row, $column, $value);

        $this->solution->fillCoordinates($row, $column, $value);

        // @todo Keep cache in sync instead of wiping it
        $this->gameCache = null;

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
            "puzzle"   => $this->puzzle->grid(),
            "solution" => $this->solution->grid(),
        ];
    }

    /**
     * Validate that there are no entries in the solution that coincide with entries in the original puzzle
     *
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     * @throws ImmutableCellException if any of the cells in the solution conflict with the puzzle
     */
    private function assertSolutionDoesntOverlapPuzzle(array $puzzle, array $solution): void
    {
        foreach ($solution as $rowId => $columns) {
            $overlap = array_intersect(array_keys($columns), array_keys($puzzle[$rowId] ?? []));
            if (!empty($overlap)) {
                throw new ImmutableCellException("The solution contains entries that are part of the puzzle");
            }
        }
    }
}
