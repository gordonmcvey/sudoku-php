<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit;

use gordonmcvey\sudoku\exception\ImmutableCellException;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\Game;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\interface\MutableGridContract;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    /**
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     * @param array<int, array<int, int>> $expected
     * @throws Exception
     */
    #[Test]
    #[DataProvider("providePuzzlesWithSolutions")]
    public function constructor(array $puzzle, array $solution, array $expected): void
    {
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);
        $solutionGrid->method("grid")->willReturn($solution);

        $game = new Game($puzzleGrid, $solutionGrid);

        $this->assertSame($expected, $game->puzzleWithSolution()->grid());
    }

    /**
     * @return array<string, array{
     *     puzzle: array<int, array<int, int>>,
     *     solution: array<int, array<int, int>>,
     *     expected: array<int, array<int, int>>
     * }>
     */
    public static function providePuzzlesWithSolutions(): array
    {
        /*
         * The values I'm using here were pulled from a completed Sudoku puzzle found online.  It should be good enough
         * for testing, but as I can't be 100% sure, I'll be replacing these test cases at a later time when I have a
         * working puzzle generator
         */
        return [
            "Full puzzle"             => [
                "puzzle"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "solution" => [],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
            ],
            "Full solution"           => [
                "puzzle"   => [],
                "solution" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
            ],
            "Fully solved row"        => [
                "puzzle"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "solution" => [
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
            ],
            "Fully solved column"     => [
                "puzzle"   => [
                    0 => [0 => 1, 2 => 3, 4 => 7, 6 => 9, 8 => 5],
                    1 => [0 => 5, 2 => 4, 4 => 3, 6 => 7, 8 => 1],
                    2 => [0 => 9, 2 => 7, 4 => 4, 6 => 3, 8 => 8],
                    3 => [0 => 3, 2 => 2, 4 => 6, 6 => 5, 8 => 9],
                    4 => [0 => 6, 2 => 1, 4 => 8, 6 => 2, 8 => 4],
                    5 => [0 => 4, 2 => 8, 4 => 9, 6 => 6, 8 => 3],
                    6 => [0 => 8, 2 => 6, 4 => 2, 6 => 1, 8 => 7],
                    7 => [0 => 2, 2 => 9, 4 => 5, 6 => 4, 8 => 6],
                    8 => [0 => 7, 2 => 5, 4 => 1, 6 => 8, 8 => 2],
                ],
                "solution" => [
                    0 => [1 => 2, 3 => 6, 5 => 8, 7 => 4],
                    1 => [1 => 8, 3 => 2, 5 => 9, 7 => 6],
                    2 => [1 => 6, 3 => 1, 5 => 5, 7 => 2],
                    3 => [1 => 7, 3 => 4, 5 => 1, 7 => 8],
                    4 => [1 => 9, 3 => 5, 5 => 3, 7 => 7],
                    5 => [1 => 5, 3 => 7, 5 => 2, 7 => 1],
                    6 => [1 => 3, 3 => 9, 5 => 4, 7 => 5],
                    7 => [1 => 1, 3 => 8, 5 => 7, 7 => 3],
                    8 => [1 => 4, 3 => 3, 5 => 6, 7 => 9],
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
            ],
            "Fully solved diagonal"   => [
                "puzzle"   => [
                    0 => [1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9],
                ],
                "solution" => [
                    0 => [0 => 1],
                    1 => [1 => 8],
                    2 => [2 => 7],
                    3 => [3 => 4],
                    4 => [4 => 8],
                    5 => [5 => 2],
                    6 => [6 => 1],
                    7 => [7 => 3],
                    8 => [8 => 2],
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
            ],
            "Fully solved diagonal 2" => [
                "puzzle"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5],
                    5 => [0 => 4, 1 => 5, 2 => 8],
                    6 => [0 => 8, 1 => 3],
                    7 => [0 => 2],
                ],
                "solution" => [
                    0 => [8 => 5],
                    1 => [7 => 6, 8 => 1],
                    2 => [6 => 3, 7 => 2, 8 => 8],
                    3 => [5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
            ],
            "Partially solved 1"      => [
                "puzzle"   => [
                    0 => [1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9],
                ],
                "solution" => [
                    0 => [0 => 1],
                    2 => [2 => 7],
                    4 => [4 => 8],
                    6 => [6 => 1],
                    8 => [8 => 2],
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
            ],
            "Partially solved 2"      => [
                "puzzle"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5],
                    5 => [0 => 4, 1 => 5, 2 => 8],
                    6 => [0 => 8, 1 => 3],
                    7 => [0 => 2],
                ],
                "solution" => [
                    0 => [8 => 5],
                    1 => [7 => 6],
                    2 => [6 => 3],
                    3 => [5 => 1],
                    4 => [4 => 8],
                    5 => [3 => 7],
                    6 => [2 => 6],
                    7 => [1 => 1],
                    8 => [0 => 7],
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 4 => 8],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7],
                    6 => [0 => 8, 1 => 3, 2 => 6],
                    7 => [0 => 2, 1 => 1],
                    8 => [0 => 7],
                ],
            ],
            "Not solved 1"            => [
                "puzzle"   => [
                    0 => [1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9],
                ],
                "solution" => [],
                "expected" => [
                    0 => [1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9],
                ],
            ],
            "Not solved 2"            => [
                "puzzle"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5],
                    5 => [0 => 4, 1 => 5, 2 => 8],
                    6 => [0 => 8, 1 => 3],
                    7 => [0 => 2],
                ],
                "solution" => [
                ],
                "expected" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5],
                    5 => [0 => 4, 1 => 5, 2 => 8],
                    6 => [0 => 8, 1 => 3],
                    7 => [0 => 2],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     * @throws Exception
     */
    #[Test]
    #[DataProvider("providePuzzlesWithCollidingSolutions")]
    public function constructorWithSolutionCollidingWithPuzzle(array $puzzle, array $solution): void
    {
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);
        $solutionGrid->method("grid")->willReturn($solution);

        $this->expectException(ImmutableCellException::class);
        new Game($puzzleGrid, $solutionGrid);
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
     * @throws Exception
     */
    #[Test]
    #[DataProvider("providePuzzlesWithSolution")]
    public function puzzleWithSolution(array $puzzle, array $solution, array $expectation): void
    {
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);
        $solutionGrid->method("grid")->willReturn($solution);

        $game = new Game($puzzleGrid, $solutionGrid);
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

    /**
     * @throws Exception
     */
    #[Test]
    public function fillCoordinates(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);
        $puzzleGrid->method("cellAtCoordinates")->with(0, 3)->willReturn(null);

        // Glass box test, the fillCoordinates will be called as the input meets the prerequisites
        $solutionGrid->expects($this->once())->method("fillCoordinates");

        $game = new Game($puzzleGrid, $solutionGrid);
        $game->fillCoordinates(0, 3, 4);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function fillCoordinatesAlreadyInPuzzle(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];

        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);
        $puzzleGrid->method("cellAtCoordinates")->with(0, 0)->willReturn($puzzle[0][0]);

        $game = new Game($puzzleGrid, $solutionGrid);
        $this->expectException(ImmutableCellException::class);
        $game->fillCoordinates(0, 0, 4);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function fillCoordinatesNonUniqueRow(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);

        $game = new Game($puzzleGrid, $solutionGrid);
        $this->expectException(InvalidRowUniqueConstraintException::class);
        $game->fillCoordinates(0, 4, 1);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function fillCoordinatesNonUniqueColumn(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);

        $game = new Game($puzzleGrid, $solutionGrid);
        $this->expectException(InvalidColumnUniqueConstraintException::class);
        $game->fillCoordinates(1, 0, 1);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function fillCoordinatesNonUniqueSubgrid(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);

        $game = new Game($puzzleGrid, $solutionGrid);
        $this->expectException(InvalidSubGridUniqueConstraintException::class);
        $game->fillCoordinates(1, 1, 1);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function fillCoordinatesDoesNotMutateForInvalidInput(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);

        // Glass box test, the fillCoordinates method on the mutable grid should never be called because it's found to
        // violate unique row/column/subgrid constraints
        $solutionGrid->expects($this->never())->method("fillCoordinates");

        $game = new Game($puzzleGrid, $solutionGrid);
        $this->expectException(InvalidRowUniqueConstraintException::class);
        $game->fillCoordinates(0, 3, 1);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function fillCoordinatesDoesNotMutateForOverlappingInput(): void
    {
        $puzzle = [0 => [0 => 1, 1 => 2, 2 => 3]];
        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);
        $puzzleGrid->method("cellAtCoordinates")->with(0, 2)->willReturn($puzzle[0][2]);

        // Glass box test, the fillCoordinates method on the mutable grid should never be called because the selected
        // solution cell is already defined in the puzzle
        $solutionGrid->expects($this->never())->method("fillCoordinates");

        $game = new Game($puzzleGrid, $solutionGrid);
        $this->expectException(ImmutableCellException::class);
        $game->fillCoordinates(0, 2, 1);
    }

    /**
     * @throws Exception
     */
    #[Test]
    public function jsonSerialize(): void
    {
        $puzzle = [
            0 => [1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
            1 => [0 => 5, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
            2 => [0 => 9, 1 => 6, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
            3 => [0 => 3, 1 => 7, 2 => 2, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
            4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
            5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 6 => 6, 7 => 1, 8 => 3],
            6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 7 => 5, 8 => 7],
            7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 8 => 6],
            8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9],
        ];
        $solution = [
            0 => [0 => 1],
            1 => [1 => 8],
            2 => [2 => 7],
            3 => [3 => 4],
            4 => [4 => 8],
            5 => [5 => 2],
            6 => [6 => 1],
            7 => [7 => 3],
            8 => [8 => 2],
        ];
        $expected = json_encode([
            "puzzle"   => $puzzle,
            "solution" => $solution,
        ]);

        $puzzleGrid = $this->createMock(GridContract::class);
        $solutionGrid = $this->createMock(MutableGridContract::class);

        $puzzleGrid->method("grid")->willReturn($puzzle);
        $solutionGrid->method("grid")->willReturn($solution);

        $game = new Game($puzzleGrid, $solutionGrid);

        $this->assertJsonStringEqualsJsonString($expected, json_encode($game));
    }
}
