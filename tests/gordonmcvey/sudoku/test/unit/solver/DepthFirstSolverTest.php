<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit\solver;

use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\interface\MutableGridContract;
use gordonmcvey\sudoku\solver\DepthFirstSolver;
use gordonmcvey\sudoku\test\support\GridMocker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\TestCase;

class DepthFirstSolverTest extends TestCase
{
    /**
     * @throws MockObjectException
     * @link https://www.youtube.com/watch?v=eAFcj_2quWI
     */
    #[Test]
    #[DataProvider("provideSolvablePuzzles")]
    public function solve(array $puzzle, array $expectation): void
    {
        $grid = $this->createMock(MutableGridContract::class);
        $grid = GridMocker::configure($grid, $puzzle);

        $solver = new DepthFirstSolver(/*new OptionFinder($grid),*/ $grid);

        $solved = $solver->solve();

        $this->assertInstanceOf(GridContract::class, $solved);
        $this->assertSame($expectation, $solved->grid());
    }

    /**
     * @return array[]
     * @todo Provide more test cases
     */
    public static function provideSolvablePuzzles(): array
    {
        return [
            "Typical puzzle 1" => [
                "puzzle" => [
                    0 => [2 => 4, 4 => 5],
                    1 => [0 => 9, 3 => 7, 4 => 3, 5 => 4, 6 => 6],
                    2 => [2 => 3, 4 => 2, 5 => 1, 7 => 4, 8 => 9],
                    3 => [1 => 3, 2 => 5, 4 => 9, 6 => 4, 7 => 8],
                    4 => [1 => 9, 7 => 3],
                    5 => [1 => 7, 2 => 6, 4 => 1, 6 => 9, 7 => 2],
                    6 => [0 => 3, 1 => 1, 3 => 9, 4 => 7, 6 => 2],
                    7 => [2 => 9, 3 => 1, 4 => 8, 5 => 2, 8 => 3],
                    8 => [4 => 6, 6 => 1],
                ],
                "expectation" => [
                    0 => [0 => 2, 1 => 6, 2 => 4, 3 => 8, 4 => 5, 5 => 9, 6 => 3, 7 => 1, 8 => 7],
                    1 => [0 => 9, 1 => 8, 2 => 1, 3 => 7, 4 => 3, 5 => 4, 6 => 6, 7 => 5, 8 => 2],
                    2 => [0 => 7, 1 => 5, 2 => 3, 3 => 6, 4 => 2, 5 => 1, 6 => 8, 7 => 4, 8 => 9],
                    3 => [0 => 1, 1 => 3, 2 => 5, 3 => 2, 4 => 9, 5 => 7, 6 => 4, 7 => 8, 8 => 6,],
                    4 => [0 => 8, 1 => 9, 2 => 2, 3 => 5, 4 => 4, 5 => 6, 6 => 7, 7 => 3, 8 => 1],
                    5 => [0 => 4, 1 => 7, 2 => 6, 3 => 3, 4 => 1, 5 => 8, 6 => 9, 7 => 2, 8 => 5],
                    6 => [0 => 3, 1 => 1, 2 => 8, 3 => 9, 4 => 7, 5 => 5, 6 => 2, 7 => 6, 8 => 4],
                    7 => [0 => 6, 1 => 4, 2 => 9, 3 => 1, 4 => 8, 5 => 2, 6 => 5, 7 => 7, 8 => 3],
                    8 => [0 => 5, 1 => 2, 2 => 7, 3 => 4, 4 => 6, 5 => 3, 6 => 1, 7 => 9, 8 => 8]
                ],
            ],
        ];
    }
}
