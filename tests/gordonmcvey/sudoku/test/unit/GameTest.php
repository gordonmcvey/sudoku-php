<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit;

use gordonmcvey\sudoku\exception\ImmutableCellException;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\Game;
use gordonmcvey\sudoku\Grid;
use gordonmcvey\sudoku\MutableGrid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    #[Test]
    public function constructor(): void
    {
        $this->markTestSkipped("Constructor tests not implemented yet");
    }

    /**
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     * @return void
     */
    #[Test]
    #[DataProvider("providePuzzlesWithCollidingSolutions")]
    public function constructorWithSolutionCollidingWithPuzzle(array $puzzle, array $solution): void
    {
        $this->expectException(ImmutableCellException::class);
        new Game(new Grid($puzzle), new MutableGrid($solution));
    }

    /**
     * @return array<string, array{
     *     puzzle: array<int, array<int, int>>,
     *     solution: array<int, array<int, int>>
     * }>
     * @todo Add more test cases
     */
    public static function providePuzzlesWithCollidingSolutions(): array
    {
        return [
            "Overlapping solution 1" => [
                "puzzle"   => [
                    1 => [
                        1 => 2,
                        3 => 4,
                        5 => 6,
                        7 => 8
                    ],
                ],
                "solution" => [
                    0 => [
                        1 => 1,
                        3 => 3,
                        5 => 5,
                        7 => 7
                    ],
                    1 => [
                        3 => 4,
                        5 => 6,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     * @param array<int, array<int, int>> $expectation
     */
    #[Test]
    #[DataProvider("providePuzzlesWithSolution")]
    public function puzzleWithSolution(array $puzzle, array $solution, array $expectation): void
    {
        $game = new Game(new Grid($puzzle), new MutableGrid($solution));
        $this->assertSame($expectation, $game->puzzleWithSolution()->grid());
    }

    /**
     * @return array<string, array{
     *     puzzle: array<int, array<int, int>>,
     *     solution: array<int, array<int, int>>,
     *     expectation: array<int, array<int, int>>,
     * }>
     */
    public static function providePuzzlesWithSolution(): array
    {
        return [
            "Puzzle with row overlap in solution" => [
                "puzzle"      => [
                    0 => [0 => 1, 2 => 2, 4 => 3, 6 => 4, 8 => 5],
                    1 => [1 => 6, 3 => 7, 5 => 8, 7 => 9],
                ],
                "solution"    => [
                    0 => [1 => 7, 3 => 6, 5 => 9, 7 => 8],
                    1 => [0 => 3, 2 => 5, 4 => 4, 6 => 1, 8 => 2],
                ],
                "expectation" => [
                    0 => [0 => 1, 1 => 7, 2 => 2, 3 => 6, 4 => 3, 5 => 9, 6 => 4, 7 => 8, 8 => 5],
                    1 => [0 => 3, 1 => 6, 2 => 5, 3 => 7, 4 => 4, 5 => 8, 6 => 1, 7 => 9, 8 => 2],
                ],
            ],
            "Puzzle with no row overlap in solution" => [
                "puzzle"      => [
                    1 => [0 => 3, 1 => 6, 2 => 5, 3 => 7, 4 => 4, 5 => 8, 6 => 1, 7 => 9, 8 => 2],
                ],
                "solution"    => [
                    0 => [0 => 1, 1 => 7, 2 => 2, 3 => 6, 4 => 3, 5 => 9, 6 => 4, 7 => 8, 8 => 5],
                ],
                "expectation" => [
                    0 => [0 => 1, 1 => 7, 2 => 2, 3 => 6, 4 => 3, 5 => 9, 6 => 4, 7 => 8, 8 => 5],
                    1 => [0 => 3, 1 => 6, 2 => 5, 3 => 7, 4 => 4, 5 => 8, 6 => 1, 7 => 9, 8 => 2],
                ],
            ],
        ];
    }

    #[Test]
    public function fillCoordinatesAlreadyInPuzzle(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $game = new Game(new Grid($puzzle), new MutableGrid());
        $this->expectException(ImmutableCellException::class);
        $game->fillCoordinates(0, 0, 4);
    }

    #[Test]
    public function fillCoordinatesNonUniqueRow(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $game = new Game(new Grid($puzzle), new MutableGrid());
        $this->expectException(InvalidRowUniqueConstraintException::class);
        $game->fillCoordinates(0, 4, 1);
    }

    #[Test]
    public function fillCoordinatesNonUniqueColumn(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $game = new Game(new Grid($puzzle), new MutableGrid());
        $this->expectException(InvalidColumnUniqueConstraintException::class);
        $game->fillCoordinates(1, 0, 1);
    }

    #[Test]
    public function fillCoordinatesNonUniqueSubgrid(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $game = new Game(new Grid($puzzle), new MutableGrid());
        $this->expectException(InvalidSubGridUniqueConstraintException::class);
        $game->fillCoordinates(1, 1, 1);
    }
}
