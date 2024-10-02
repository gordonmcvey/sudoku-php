<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit;

use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\ImmutableCellException;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use gordonmcvey\sudoku\exception\InvalidGridInsertionUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\Grid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Random\Randomizer;
use TypeError;

class GridTest extends TestCase
{
    /**
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     */
    #[Test]
    #[DataProvider("provideForConstructor")]
    public function constructor(array $puzzle, array $solution): void
    {
        $grid = new Grid(puzzle: $puzzle, solution: $solution);

        $this->assertSame($puzzle, $grid->puzzle());
        $this->assertSame($solution, $grid->solution());
    }

    /**
     * @return array<string, array{
     *     puzzle: array<int, array<int, int>>,
     *     solution: array<int, array<int, int>>
     * }>
     */
    public static function provideForConstructor(): array
    {
        /*
         * The values I'm using here were pulled from a completed Sudoku puzzle found online.  It should be good enough
         * for testing, but as I can't be 100% sure, I'll be replacing these test cases at a later time when I have a
         * working puzzle generator
         */
        return [
            "Full puzzle" => [
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
            ],
            "Full solution" => [
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
            ],
            "fully solved 1" => [
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
            ],
            "Partially solved 1" => [
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
            ],
            "Not solved 1" => [
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
            ],
            "Fully solved 2" => [
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
            ],
            "Partially solved 2" => [
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
            ],
            "Not solved 2" => [
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
            ],
        ];
    }

    #[Test]
    public function constructorWithNoArguments(): void
    {
        $grid = new Grid();

        $this->assertEmpty($grid->puzzle());
        $this->assertEmpty($grid->solution());
    }

    /**
     * @param array<array-key, array<array-key, mixed>> $puzzle
     * @return void
     */
    #[Test]
    #[DataProvider("providePuzzlesWithInvalidKeyTypes")]
    public function constructorWithPuzzlesWithInvalidKeyTypes(array $puzzle): void
    {
        $this->expectException(TypeError::class);
        new Grid(puzzle: $puzzle);
    }

    /**
     * @return array<array-key, array{
     *     puzzle: array<array-key, array<array-key, mixed>>
     * }>
     */
    public static function providePuzzlesWithInvalidKeyTypes(): array
    {
        return [
            "Invalid row key" => [
                "puzzle" => [
                    0 => [0 => 1, 1 => 2, 2 => 3],
                    "one" => [0 => 4, 1 => 5, 2 => 6],
                    2 => [0 => 7, 1 => 8, 2 => 9],
                ],
            ],
            "Invalid column key" => [
                "puzzle" => [
                    0 => [0 => 1, 1 => 2, 2 => 3],
                    1 => [0 => 4, 1 => 5, 2 => 6],
                    2 => [0 => 7, "one" => 8, 2 => 9],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, mixed>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithInvalidValueTypes")]
    public function constructorWithPuzzleValuesOfInvalidType(array $grid): void
    {
        $this->expectException(TypeError::class);
        new Grid(puzzle: $grid);
    }

    /**
     * @param array<int, array<int, mixed>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithInvalidValueTypes")]
    public function constructorWithSolutionValuesOfInvalidType(array $grid): void
    {
        $this->expectException(TypeError::class);
        new Grid(solution: $grid);
    }

    /**
     * @return array<string, array{
     *     grid: array<int, array<int, mixed>>
     * }>
     */
    public static function provideGridsWithInvalidValueTypes(): array
    {
        return [
            "Contains a float" => [
                "grid" => [
                    0 => [1 => 2, 3 => 4, 5 => 6, 7 => 8.1, 8 => 9],
                ],
            ],
            "Contains a string" => [
                "grid" => [
                    0 => [1 => 2, 3 => 4, 5 => 6, 7 => "8", 8 => 9],
                ],
            ],
            "Contains a boolean" => [
                "grid" => [
                    0 => [1 => 2, 3 => 4, 5 => 6, 7 => true, 8 => 9],
                ],
            ],
            "Contains an array" => [
                "grid" => [
                    0 => [1 => 2, 3 => 4, 5 => 6, 7 => [8], 8 => 9],
                ],
            ],
            "Contains an object" => [
                "grid" => [
                    0 => [1 => 2, 3 => 4, 5 => 6, 7 => (object)[8], 8 => 9],
                ],
            ],
            "Contains null" => [
                "grid" => [
                    0 => [1 => 2, 3 => 4, 5 => 6, 7 => null, 8 => 9],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithKeysOutOfRange")]
    public function constructorWithPuzzleKeysOutOfRange(array $grid): void
    {
        $this->expectException(InvalidGridCoordsException::class);
        new Grid(puzzle: $grid);
    }

    /**
     * @return array<string, array{
     *     grid: array<int, array<int, int>>
     * }>
     */
    public static function provideGridsWithKeysOutOfRange(): array
    {
        return [
            "Row key below 0" => [
                "grid" => [
                    -1 => [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9],
                ],
            ],
            "Row key above 8" => [
                "grid" => [
                    10 => [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9],
                ],
            ],
            "Column key below 0" => [
                "grid" => [
                    0 => [ -1 => 1, 0 => 2, 1 => 3, 2 => 4, 3 => 5, 4 => 6, 5 => 7, 6 => 8, 7 => 9],
                ],
            ],
            "Column key above 8" => [
                "grid" => [
                    0 => [ 1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithValuesOutOfRange")]
    public function constructorWithPuzzleValuesOutOfRange(array $grid): void
    {
        $this->expectException(CellValueRangeException::class);
        new Grid(puzzle: $grid);
    }

    /**
     * @param array<int, array<int, int>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithValuesOutOfRange")]
    public function constructorWithSolutionValuesOutOfRange(array $grid): void
    {
        $this->expectException(CellValueRangeException::class);
        new Grid(solution: $grid);
    }

    /**
     * @return array<string, array{
     *     grid: array<int, array<int, int>>
     * }>
     */
    public static function provideGridsWithValuesOutOfRange(): array
    {
        return [
            "Value below 0" => [
                "grid" => [
                    0 => [0 => 0, 1 => 2, 2 => 3, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8],
                ],
            ],
            "Value above 9" => [
                "grid" => [
                    0 => [0 => 2, 1 => 3, 2 => 4, 3 => 5, 4 => 6, 5 => 7, 6 => 8, 7 => 9, 8 => 10],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     * @return void
     */
    #[Test]
    #[DataProvider("providePuzzlesWithCollidingSolutions")]
    public function constructorWithSolutionValuesCollidingWithPuzzleValues(array $puzzle, array $solution): void
    {
        $this->expectException(ImmutableCellException::class);
        new Grid(puzzle: $puzzle, solution: $solution);
    }

    /**
     * @param array<int, array<int, mixed>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithNonUniqueRows")]
    public function constructorWithPuzzleWithNonUniqueRows(array $grid): void
    {
        $this->expectException(InvalidRowUniqueConstraintException::class);
        new Grid(puzzle: $grid);
    }

    /**
     * @param array<int, array<int, mixed>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithNonUniqueRows")]
    public function constructorWithSolutionWithNonUniqueRows(array $grid): void
    {
        $this->expectException(InvalidRowUniqueConstraintException::class);
        new Grid(solution: $grid);
    }

    /**
     * @return array<string, array{
     *     grid: array<int, array<int, int>>
     * }>
     * @todo Add more test cases
     */
    public static function provideGridsWithNonUniqueRows(): array
    {
        return [
            "Non-unique row case 1" => [
                "grid" => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 6, 7 => 7, 8 => 7]
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithNonUniqueColumns")]
    public function constructorWithPuzzleWithNonUniqueColumns(array $grid): void
    {
        $this->expectException(InvalidColumnUniqueConstraintException::class);
        new Grid(puzzle: $grid);
    }

    /**
     * @param array<int, array<int, int>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithNonUniqueColumns")]
    public function constructorWithSolutionWithNonUniqueColumns(array $grid): void
    {
        $this->expectException(InvalidColumnUniqueConstraintException::class);
        new Grid(solution: $grid);
    }

    /**
     * @return array<string, array{
     *     grid: array<int, array<int, int>>
     * }>
     * @todo Add more test cases
     */
    public static function provideGridsWithNonUniqueColumns(): array
    {
        return [
            "Non-unique column case 1" => [
                "grid" => [
                    0 => [0 => 1],
                    1 => [0 => 2],
                    2 => [0 => 3],
                    3 => [0 => 4],
                    4 => [0 => 5],
                    5 => [0 => 6],
                    6 => [0 => 6],
                    7 => [0 => 7],
                    8 => [0 => 7],
                ],
            ],
        ];
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
                        3 => 1,
                        5 => 3,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $puzzle
     */
    #[Test]
    #[DataProvider("providePuzzles")]
    public function puzzle(array $puzzle): void
    {
        $grid = new Grid(puzzle: $puzzle);
        $this->assertSame($puzzle, $grid->puzzle());
    }

    /**
     * @return array<string, array{
     *     puzzle: array<int, array<int, int>>,
     * }>
     * @todo Add more test cases
     */
    public static function providePuzzles(): array
    {
        return [
            "Puzzle 1" => [
                "puzzle" => [
                    0 => [0 => 1, 2 => 3, 4 => 5, 6 => 7, 8 => 9],
                ],
            ],
            "Puzzle 2" => [
                "puzzle" => [
                    1 => [1 => 2, 3 => 4, 5 => 6, 7 => 8],
                ],
            ]
        ];
    }

    /**
     * @param array<int, array<int, int>> $solution
     */
    #[Test]
    #[DataProvider("provideSolutions")]
    public function solution(array $solution): void
    {
        $grid = new Grid(solution: $solution);
        $this->assertSame($solution, $grid->solution());
    }

    /**
     * @return array<string, array{
     *     solution: array<int, array<int, int>>
     * }>
     * @todo Add more test cases
     */
    public static function provideSolutions(): array
    {
        return [
            "Solution 1" => [
                "solution" => [
                    0 => [0 => 1, 2 => 3, 4 => 5, 6 => 7, 8 => 9],
                ],
            ],
            "solution 2" => [
                "solution" => [
                    1 => [1 => 2, 3 => 4, 5 => 6, 7 => 8],
                ],
            ]
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
        $grid = new Grid(puzzle: $puzzle, solution: $solution);
        $this->assertSame($expectation, $grid->puzzleWithSolution());
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
     * @param array<int, array<int, int>> $puzzle
     * @param array<array-key, array{
     *     row: int,
     *     column: int,
     *     value: int|null
     * }> $expectations
     */
    #[Test]
    #[DataProvider("provideCellAtCoordinatesFromPuzzle")]
    public function cellAtCoordinates(array $puzzle, array $expectations): void
    {
        $grid = new Grid(puzzle: $puzzle);
        foreach ($expectations as $expectation) {
            $this->assertSame(
                $expectation["value"],
                $grid->cellAtCoordinates($expectation["row"], $expectation["column"]),
            );
        }
    }

    /**
     * @return array<string, array{
     *     puzzle: array<int, array<int, int>>,
     *     expectations: array<array-key, array{
     *         row: int,
     *         column: int,
     *         value: int|null
     *     }>
     * }>
     */
    public static function provideCellAtCoordinatesFromPuzzle(): array
    {
        return [
            "First two rows" => [
                "puzzle"   => [
                    0 => [
                        0 => 1,
                        2 => 3,
                        4 => 5,
                        6 => 7,
                        8 => 9,
                    ],
                    1 => [
                        1 => 2,
                        3 => 4,
                        5 => 6,
                        7 => 8,
                    ],
                ],
                "expectations" => [
                    ["row" => 0, "column" => 0, "value" => 1],
                    ["row" => 0, "column" => 1, "value" => null],
                    ["row" => 0, "column" => 2, "value" => 3],
                    ["row" => 0, "column" => 3, "value" => null],
                    ["row" => 0, "column" => 4, "value" => 5],
                    ["row" => 0, "column" => 5, "value" => null],
                    ["row" => 0, "column" => 6, "value" => 7],
                    ["row" => 0, "column" => 7, "value" => null],
                    ["row" => 0, "column" => 8, "value" => 9],
                    ["row" => 1, "column" => 0, "value" => null],
                    ["row" => 1, "column" => 1, "value" => 2],
                    ["row" => 1, "column" => 2, "value" => null],
                    ["row" => 1, "column" => 3, "value" => 4],
                    ["row" => 1, "column" => 4, "value" => null],
                    ["row" => 1, "column" => 5, "value" => 6],
                    ["row" => 1, "column" => 6, "value" => null],
                    ["row" => 1, "column" => 7, "value" => 8],
                    ["row" => 1, "column" => 0, "value" => null],
                ],
            ],
            "Last two rows" => [
                "puzzle"   => [
                    7 => [
                        0 => 9,
                        2 => 7,
                        4 => 5,
                        6 => 3,
                        8 => 1,
                    ],
                    8 => [
                        1 => 8,
                        3 => 6,
                        5 => 4,
                        7 => 2,
                    ],
                ],
                "expectations" => [
                    ["row" => 7, "column" => 0, "value" => 9],
                    ["row" => 7, "column" => 1, "value" => null],
                    ["row" => 7, "column" => 2, "value" => 7],
                    ["row" => 7, "column" => 3, "value" => null],
                    ["row" => 7, "column" => 4, "value" => 5],
                    ["row" => 7, "column" => 5, "value" => null],
                    ["row" => 7, "column" => 6, "value" => 3],
                    ["row" => 7, "column" => 7, "value" => null],
                    ["row" => 7, "column" => 8, "value" => 1],
                    ["row" => 8, "column" => 0, "value" => null],
                    ["row" => 8, "column" => 1, "value" => 8],
                    ["row" => 8, "column" => 2, "value" => null],
                    ["row" => 8, "column" => 3, "value" => 6],
                    ["row" => 8, "column" => 4, "value" => null],
                    ["row" => 8, "column" => 5, "value" => 4],
                    ["row" => 8, "column" => 6, "value" => null],
                    ["row" => 8, "column" => 7, "value" => 2],
                    ["row" => 8, "column" => 0, "value" => null],
                ],
            ],
        ];
    }

    /**
     * @param array<array-key, array{
     *     row: int,
     *     column: int,
     *     value: int
     * }> $entries
     * @param array<int, array<int, int>> $expectation
     */
    #[Test]
    #[DataProvider("provideForFillCoordinates")]
    public function fillCoordinates(array $entries, array $expectation): void
    {
        $grid = new Grid();

        foreach ($entries as $entry) {
            $grid->fillCoordinates($entry["row"], $entry["column"], $entry["value"]);
        }

        $this->assertSame($expectation, $grid->solution());
    }

    /**
     * @return array<string, array{
     *     entries: array<array-key, array{
     *         row: int,
     *         column: int,
     *         value: int
     *     }>,
     *     expectation: array<int, array<int, int>>
     * }>
     */
    public static function provideForFillCoordinates(): array
    {
        return [
            "Typical case" => [
                "entries"     => [
                    ["row" => 0, "column" => 0, "value" => 1],
                    ["row" => 0, "column" => 1, "value" => 7],
                    ["row" => 0, "column" => 2, "value" => 2],
                    ["row" => 0, "column" => 3, "value" => 6],
                    ["row" => 0, "column" => 4, "value" => 3],
                    ["row" => 0, "column" => 5, "value" => 9],
                    ["row" => 0, "column" => 6, "value" => 4],
                    ["row" => 0, "column" => 7, "value" => 8],
                    ["row" => 0, "column" => 8, "value" => 5],
                    ["row" => 1, "column" => 0, "value" => 3],
                    ["row" => 1, "column" => 1, "value" => 6],
                    ["row" => 1, "column" => 2, "value" => 5],
                    ["row" => 1, "column" => 3, "value" => 7],
                    ["row" => 1, "column" => 4, "value" => 4],
                    ["row" => 1, "column" => 5, "value" => 8],
                    ["row" => 1, "column" => 6, "value" => 1],
                    ["row" => 1, "column" => 7, "value" => 9],
                    ["row" => 1, "column" => 8, "value" => 2],
                ],
                "expectation" => [
                    0 => [0 => 1, 1 => 7, 2 => 2, 3 => 6, 4 => 3, 5 => 9, 6 => 4, 7 => 8, 8 => 5],
                    1 => [0 => 3, 1 => 6, 2 => 5, 3 => 7, 4 => 4, 5 => 8, 6 => 1, 7 => 9, 8 => 2],
                ],
            ],
            "Shuffled entries" => [
                "entries"     => (new Randomizer())->shuffleArray([
                    ["row" => 0, "column" => 0, "value" => 1],
                    ["row" => 0, "column" => 1, "value" => 7],
                    ["row" => 0, "column" => 2, "value" => 2],
                    ["row" => 0, "column" => 3, "value" => 6],
                    ["row" => 0, "column" => 4, "value" => 3],
                    ["row" => 0, "column" => 5, "value" => 9],
                    ["row" => 0, "column" => 6, "value" => 4],
                    ["row" => 0, "column" => 7, "value" => 8],
                    ["row" => 0, "column" => 8, "value" => 5],
                    ["row" => 1, "column" => 0, "value" => 3],
                    ["row" => 1, "column" => 1, "value" => 6],
                    ["row" => 1, "column" => 2, "value" => 5],
                    ["row" => 1, "column" => 3, "value" => 7],
                    ["row" => 1, "column" => 4, "value" => 4],
                    ["row" => 1, "column" => 5, "value" => 8],
                    ["row" => 1, "column" => 6, "value" => 1],
                    ["row" => 1, "column" => 7, "value" => 9],
                    ["row" => 1, "column" => 8, "value" => 2],
                ]),
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
        $grid = new Grid($puzzle);

        $this->expectException(ImmutableCellException::class);
        $grid->fillCoordinates(0, 0, 4);
    }

    /**
     * @param array<array-key, array{
     *     row: int,
     *     column: int,
     *     value: int
     * }> $entries
     */
    #[Test]
    #[DataProvider("provideForFillCoordinatesViolatesUniqueConstraints")]
    public function fillCoordinatesViolatesUniqueConstraints(array $entries): void
    {
        $grid = new Grid();

        $this->expectException(InvalidGridInsertionUniqueConstraintException::class);
        foreach ($entries as $entry) {
            $grid->fillCoordinates($entry["row"], $entry["column"], $entry["value"]);
        }
    }

    /**
     * @return array<string, array{
     *     entries: array<array-key, array{
     *         row: int,
     *         column: int,
     *         value: int
     *     }>
     * }>
     * @todo Implement test case for subgrid constraint check
     */
    public static function provideForFillCoordinatesViolatesUniqueConstraints(): array
    {
        return [
            "Violates row constraint" => [
                "entries" => [
                    ["row" => 0, "column" => 0, "value" => 1],
                    ["row" => 0, "column" => 1, "value" => 1],
                ],
            ],
            "Violates column constraint" => [
                "entries" => [
                    ["row" => 0, "column" => 0, "value" => 1],
                    ["row" => 1, "column" => 0, "value" => 1],
                ],
            ]
        ];
    }

    /**
     * @param array<int, array<int, int>> $puzzle
     * @param array<int, array<int, int>> $solution
     */
    #[Test]
    #[DataProvider("provideForConstructor")]
    public function jsonSerialize(array $puzzle, array $solution): void
    {
        $grid = new Grid(puzzle: $puzzle, solution: $solution);
        $this->assertSame(
            [
                "puzzle"   => $puzzle,
                "solution" => $solution,
            ],
            $grid->jsonSerialize(),
        );
    }
}
