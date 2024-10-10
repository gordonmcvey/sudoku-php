<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit;

use gordonmcvey\sudoku\exception\CellValueRangeException;
use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidGridCoordsException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\Grid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TypeError;

class GridTest extends TestCase
{
    /**
     * @param array<int, array<int, int>> $gridState
     */
    #[Test]
    #[DataProvider("provideForConstructor")]
    public function constructor(array $gridState): void
    {
        $grid = new Grid(gridState: $gridState);
        $this->assertSame($gridState, $grid->grid());
    }

    /**
     * @return array<string, array{
     *     gridState: array<int, array<int, int>>,
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
            "Full Grid" => [
                "gridState"   => [
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
            "Empty Grid" => [
                "gridState"   => [],
            ],
            "Partial Grid 1" => [
                "gridState"   => [
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
            "Partial Grid 2" => [
                "gridState"   => [
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

    #[Test]
    public function constructorWithNoArguments(): void
    {
        $grid = new Grid();

        $this->assertEmpty($grid->grid());
    }

    /**
     * @param array<array-key, array<array-key, mixed>> $gridState
     * @return void
     */
    #[Test]
    #[DataProvider("provideGridsWithInvalidKeyTypes")]
    public function constructorWithInvalidKeyTypes(array $gridState): void
    {
        $this->expectException(TypeError::class);
        new Grid(gridState: $gridState);
    }

    /**
     * @return array<array-key, array{
     *     gridState: array<array-key, array<array-key, mixed>>
     * }>
     */
    public static function provideGridsWithInvalidKeyTypes(): array
    {
        return [
            "Invalid row key" => [
                "gridState" => [
                    0 => [0 => 1, 1 => 2, 2 => 3],
                    "one" => [0 => 4, 1 => 5, 2 => 6],
                    2 => [0 => 7, 1 => 8, 2 => 9],
                ],
            ],
            "Invalid column key" => [
                "gridState" => [
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
    public function constructorWithInvalidValueTypes(array $grid): void
    {
        $this->expectException(TypeError::class);
        new Grid(gridState: $grid);
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
    public function constructorWithKeysOutOfRange(array $grid): void
    {
        $this->expectException(InvalidGridCoordsException::class);
        new Grid(gridState: $grid);
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
    public function constructorWithValuesOutOfRange(array $grid): void
    {
        $this->expectException(CellValueRangeException::class);
        new Grid(gridState: $grid);
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
     * @param array<int, array<int, mixed>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithNonUniqueRows")]
    public function constructorWithNonUniqueRows(array $grid): void
    {
        $this->expectException(InvalidRowUniqueConstraintException::class);
        new Grid(gridState: $grid);
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
        new Grid(gridState: $grid);
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
     * @param array<int, array<int, int>> $grid
     */
    #[Test]
    #[DataProvider("provideGridsWithNonUniqueSubGrids")]
    public function constructorWithNonUniqueSubGrids(array $grid): void
    {
        $this->expectException(InvalidSubGridUniqueConstraintException::class);
        new Grid(gridState: $grid);
    }

    /**
     * @return array<string, array{
     *     grid: array<int, array<int, int>>
     * }>
     * @todo Add more test cases
     */
    public static function provideGridsWithNonUniqueSubGrids(): array
    {
        return [
            "Non-unique subgrid case 1" => [
                "grid" => [
                    0 => [0 => 1, 1 => 2, 2 => 3],
                    1 => [0 => 2, 1 => 3, 2 => 4],
                    2 => [0 => 3, 1 => 4, 2 => 5],
                ],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $gridState
     * @param array<array-key, array{
     *     row: int,
     *     column: int,
     *     value: int|null
     * }> $expectations
     */
    #[Test]
    #[DataProvider("provideCellAtCoordinatesFromPuzzle")]
    public function cellAtCoordinates(array $gridState, array $expectations): void
    {
        $grid = new Grid(gridState: $gridState);
        foreach ($expectations as $expectation) {
            $this->assertSame(
                $expectation["value"],
                $grid->cellAtCoordinates($expectation["row"], $expectation["column"]),
            );
        }
    }

    /**
     * @return array<string, array{
     *     gridState: array<int, array<int, int>>,
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
                "gridState"   => [
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
                "gridState"   => [
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
     * @param array<int, array<int, int>> $gridState
     */
    #[Test]
    #[DataProvider("provideForConstructor")]
    public function jsonSerialize(array $gridState): void
    {
        $grid = new Grid(gridState: $gridState);
        $this->assertEquals(
            $gridState,
            $grid->jsonSerialize(),
        );
    }

    /**
     * @param array<int, array<int, int>> $gridState
     * @param array<int> $expectation
     */
    #[Test]
    #[DataProvider("provideRows")]
    public function row(int $rowId, array $gridState, array $expectation): void
    {
        $grid = new Grid(gridState: $gridState);
        $this->assertSame($expectation, $grid->row($rowId));
    }

    /**
     * @return array<string, array{
     *     rowId: int,
     *     gridState: array<int, array<int, int>>,
     *     expectation: array<int>
     * }>
     */
    public static function provideRows(): array
    {
        return [
            "Partial row 1" => [
                "rowId"       => 0,
                "gridState"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7],
                ],
                "expectation" => [1, 2, 3, 6, 7],
            ],
            "Partial row 2" => [
                "rowId"       => 1,
                "gridState"   => [
                    1 => [5 => 9, 6 => 7, 7 => 6, 8 => 1],
                ],
                "expectation" => [9, 7, 6, 1],
            ],
            "Full row"      => [
                "rowId"       => 2,
                "gridState"   => [
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                ],
                "expectation" => [9, 6, 7, 1, 4, 5, 3, 2, 8]
            ],
            "Empty row"     => [
                "rowId"       => 3,
                "gridState"   => [],
                "expectation" => [],
            ]
        ];
    }

    /**
     * @param array<int, array<int, int>> $gridState
     * @param array<int> $expectation
     */
    #[Test]
    #[DataProvider("provideColumns")]
    public function column(int $columnId, array $gridState, array $expectation): void
    {
        $grid = new Grid(gridState: $gridState);
        $this->assertSame($expectation, $grid->column($columnId));
    }

    /**
     * @return array<string, array{
     *     columnId: int,
     *     gridState: array<int, array<int, int>>,
     *     expectation: array<int>
     * }>
     */
    public static function provideColumns(): array
    {
        return [
            "Partial column 1" => [
                "columnId"    => 0,
                "gridState"   => [
                    0 => [0 => 1],
                    1 => [0 => 5],
                    2 => [0 => 9],
                    3 => [0 => 3],
                    4 => [0 => 6],
                ],
                "expectation" => [1, 5, 9, 3, 6],
            ],
            "Partial column 2" => [
                "columnId"    => 1,
                "gridState"   => [
                    5 => [1 => 5],
                    6 => [1 => 3],
                    7 => [1 => 1],
                    8 => [1 => 4],
                ],
                "expectation" => [5, 3, 1, 4],
            ],
            "Full Column"      => [
                "columnId"    => 2,
                "gridState"   => [
                    0 => [2 => 3],
                    1 => [2 => 4],
                    2 => [2 => 7],
                    3 => [2 => 2],
                    4 => [2 => 1],
                    5 => [2 => 8],
                    6 => [2 => 6],
                    7 => [2 => 9],
                    8 => [2 => 5],
                ],
                "expectation" => [3, 4, 7, 2, 1, 8, 6, 9, 5],
            ],
            "Empty column"     => [
                "columnId"    => 3,
                "gridState"   => [],
                "expectation" => [],
            ],
        ];
    }

    /**
     * @param array<int, array<int, int>> $gridState
     * @param array<int> $expectation
     */
    #[Test]
    #[DataProvider("provideSubGrids")]
    public function subGrid(int $subGridId, array $gridState, array $expectation): void
    {
        $grid = new Grid(gridState: $gridState);
        $this->assertSame($expectation, $grid->subGrid($subGridId));
    }

    /**
     * @return array<string, array{
     *     subGridId: int,
     *     gridState: array<int, array<int, int>>,
     *     expectation: array<int>
     * }>
     */
    public static function provideSubGrids(): array
    {
        return [
            "Partial subgrid 1" => [
                "subGridId"   => 0,
                "gridState"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3],
                ],
                "expectation" => [1, 2, 3],
            ],
            "Partial subgrid 2" => [
                "subGridId"   => 1,
                "gridState"   => [
                    0 => [3 => 6],
                    1 => [3 => 2],
                    2 => [3 => 1],
                ],
                "expectation" => [6, 2, 1],
            ],
            "Full subgrid"      => [
                "subGridId"   => 2,
                "gridState"   => [
                    0 => [6 => 9, 7 => 4, 8 => 5],
                    1 => [6 => 7, 7 => 6, 8 => 1],
                    2 => [6 => 3, 7 => 2, 8 => 8],
                ],
                "expectation" => [9, 4, 5, 7, 6, 1, 3, 2, 8],
            ],
            "Empty subgrid"     => [
                "subGridId"   => 3,
                "gridState"   => [
                ],
                "expectation" => [],
            ],
        ];
    }


    /**
     * @param array<int, array<int, int>> $gridState
     * @param array<int> $expectation
     */
    #[Test]
    #[DataProvider("provideSubGridsForCoordinates")]
    public function subGridAtCoordinates(int $rowId, int $columnId, array $gridState, array $expectation): void
    {
        $grid = new Grid(gridState: $gridState);
        $this->assertSame($expectation, $grid->subGridAtCoordinates($rowId, $columnId));
    }

    /**
     * @return array<string, array{
     *     rowId: int,
     *     columnId: int,
     *     gridState: array<int, array<int, int>>,
     *     expectation: array<int>
     * }>
     */
    public static function provideSubGridsForCoordinates(): array
    {
        return [
            "Partial subgrid 1" => [
                "rowId"       => 1,
                "columnId"    => 1,
                "gridState"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3],
                ],
                "expectation" => [1, 2, 3],
            ],
            "Partial subgrid 2" => [
                "rowId"       => 1,
                "columnId"    => 4,
                "gridState"   => [
                    0 => [3 => 6],
                    1 => [3 => 2],
                    2 => [3 => 1],
                ],
                "expectation" => [6, 2, 1],
            ],
            "Full subgrid"      => [
                "rowId"       => 2,
                "columnId"    => 7,
                "gridState"   => [
                    0 => [6 => 9, 7 => 4, 8 => 5],
                    1 => [6 => 7, 7 => 6, 8 => 1],
                    2 => [6 => 3, 7 => 2, 8 => 8],
                ],
                "expectation" => [9, 4, 5, 7, 6, 1, 3, 2, 8],
            ],
            "Empty subgrid"     => [
                "rowId"       => 4,
                "columnId"    => 1,
                "gridState"   => [
                ],
                "expectation" => [],
            ],
        ];
    }

    #[Test]
    public function isEmptyMethod(): void
    {
        $grid = new Grid();
        $this->assertTrue($grid->isEmpty());
    }

    #[Test]
    public function isNotEmptyMethod(): void
    {
        $grid = new Grid([0 => [0 => 1]]);
        $this->assertFalse($grid->isEmpty());
    }
}
