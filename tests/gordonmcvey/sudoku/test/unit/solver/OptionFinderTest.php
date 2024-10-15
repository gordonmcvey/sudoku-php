<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit\solver;

use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\solver\OptionFinder;
use gordonmcvey\sudoku\util\SubGridMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Exception as MockException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OptionFinderTest extends TestCase
{
    /**
     * @param array<int, array<int, int>> $gridState
     * @param array<int, array<int, array<int>>> $expectation
     * @throws MockException
     */
    #[Test]
    #[DataProvider("provideGrids")]
    public function findOptionsFor(array $gridState, array $expectation): void
    {
        $grid = $this->mockGrid($gridState);
        $finder = new OptionFinder();
        $options = $finder->findOptionsFor($grid);

        $this->assertSame($expectation, $options);
    }

    /**
     * @return array<string, array{
     *     gridState: array<int, array<int, int>>,
     *     expectation: array<int, array<int, array<int>>>
     * }>
     */
    public static function provideGrids(): array
    {
        return [
            "Already solved"    => [
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
                "expectation" => [],
            ],
            "Solve for row"     => [
                "gridState"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 4 => 6, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 4 => 9, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "expectation" => [
                    4 => [
                        0 => [6],
                        1 => [9],
                        2 => [1],
                        3 => [5],
                        4 => [8],
                        5 => [3],
                        6 => [2],
                        7 => [7],
                        8 => [4],
                    ],
                ],
            ],
            "Solve for column"  => [
                "gridState"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 3 => 4, 5 => 1, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 3 => 5, 5 => 3, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 3 => 7, 5 => 2, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "expectation" => [
                    0 => [
                        4 => [7],
                    ],
                    1 => [
                        4 => [3],
                    ],
                    2 => [
                        4 => [4],
                    ],
                    3 => [
                        4 => [6],
                    ],
                    4 => [
                        4 => [8],
                    ],
                    5 => [
                        4 => [9],
                    ],
                    6 => [
                        4 => [2],
                    ],
                    7 => [
                        4 => [5],
                    ],
                    8 => [
                        4 => [1],
                    ],
                ],
            ],
            "Solve for subgrid" => [
                "gridState"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3, 3 => 6, 4 => 7, 5 => 8, 6 => 9, 7 => 4, 8 => 5],
                    1 => [0 => 5, 1 => 8, 2 => 4, 3 => 2, 4 => 3, 5 => 9, 6 => 7, 7 => 6, 8 => 1],
                    2 => [0 => 9, 1 => 6, 2 => 7, 3 => 1, 4 => 4, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 1 => 7, 2 => 2, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 2 => 1, 6 => 2, 7 => 7, 8 => 4],
                    5 => [0 => 4, 1 => 5, 2 => 8, 6 => 6, 7 => 1, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 5 => 4, 6 => 1, 7 => 5, 8 => 7],
                    7 => [0 => 2, 1 => 1, 2 => 9, 3 => 8, 4 => 5, 5 => 7, 6 => 4, 7 => 3, 8 => 6],
                    8 => [0 => 7, 1 => 4, 2 => 5, 3 => 3, 4 => 1, 5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "expectation" => [
                    3 => [
                        3 => [4],
                        4 => [6],
                        5 => [1],
                    ],
                    4 => [
                        3 => [5],
                        4 => [8],
                        5 => [3],
                    ],
                    5 => [
                        3 => [7],
                        4 => [9],
                        5 => [2],
                    ],
                ],
            ],
            "Multiple options"  => [
                "gridState"   => [
                    0 => [1 => 2, 2 => 3, 3 => 6, 5 => 8, 6 => 9, 8 => 5],
                    1 => [0 => 5, 1 => 8, 3 => 2, 4 => 3, 5 => 9, 7 => 6],
                    2 => [0 => 9, 1 => 6, 5 => 5, 6 => 3, 7 => 2, 8 => 8],
                    3 => [0 => 3, 2 => 2, 4 => 6, 6 => 5, 7 => 8, 8 => 9],
                    4 => [0 => 6, 1 => 9, 3 => 5, 4 => 8, 5 => 3, 6 => 2],
                    5 => [1 => 5, 2 => 8, 4 => 9, 5 => 2, 6 => 6, 8 => 3],
                    6 => [0 => 8, 1 => 3, 2 => 6, 3 => 9, 4 => 2, 7 => 5],
                    7 => [0 => 2, 2 => 9, 3 => 8, 4 => 5, 7 => 3, 8 => 6],
                    8 => [2 => 5, 3 => 3 ,5 => 6, 6 => 8, 7 => 9, 8 => 2],
                ],
                "expectation" => [
                    0 => [
                        0 => [1, 4, 7],
                        4 => [1, 4, 7],
                        7 => [1, 4, 7],
                    ],
                    1 => [
                        2 => [1, 4, 7],
                        6 => [1, 4, 7],
                        8 => [1, 4, 7],
                    ],
                    2 => [
                        2 => [1, 4, 7],
                        3 => [1, 4, 7],
                        4 => [1, 4, 7],
                    ],
                    3 => [
                        1 => [1, 4, 7],
                        3 => [1, 4, 7],
                        5 => [1, 4, 7],
                    ],
                    4 => [
                        2 => [1, 4, 7],
                        7 => [1, 4, 7],
                        8 => [1, 4, 7],
                    ],
                    5 => [
                        0 => [1, 4, 7],
                        3 => [1, 4, 7],
                        7 => [1, 4, 7],
                    ],
                    6 => [
                        5 => [1, 4, 7],
                        6 => [1, 4, 7],
                        8 => [1, 4, 7],
                    ],
                    7 => [
                        1 => [1, 4, 7],
                        5 => [1, 4, 7],
                        6 => [1, 4, 7],
                    ],
                    8 => [
                        0 => [1, 4, 7],
                        1 => [1, 4, 7],
                        4 => [1, 4, 7],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws MockException
     */
    #[Test]
    public function findOptionsFoeEmpty(): void
    {
        $grid = $this->createMock(GridContract::class);
        $grid->expects($this->once())->method('isEmpty')->willReturn(true);

        $finder = new OptionFinder();
        $options = $finder->findOptionsFor($grid);

        $this->assertCount(9, $options);
        foreach ($options as $row) {
            $this->assertCount(9, $row);
            foreach ($row as $option) {
                $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9], $option);
            }
        }
    }

    /**
     * @param array<int, array<int, int>> $gridState
     * @throws MockException
     */
    private function mockGrid(array $gridState): GridContract&MockObject
    {
        $grid = $this->createMock(GridContract::class);
        $grid->expects($this->once())->method("isEmpty")->willReturn(false);

        $grid->method("cellAtCoordinates")
            ->willReturnCallback(fn (int $row, int $column): ?int => $gridState[$row][$column] ?? null);

        $grid->method("row")
            ->willReturnCallback(fn (int $row): array => $gridState[$row] ?? []);

        $grid->method("column")
            ->willReturnCallback(fn (int $column): array => array_column($gridState, $column));

        // This is a bit of a cheat because we're using the SubGridMapper to implement this method for the mock, but
        // if we didn't, we'd basically have to re-implement the same logic for the mock.  I feel that this is an
        // acceptable break from the normal rules of unit tests.
        $grid->method("subGridAtCoordinates")->willReturnCallback(
            fn (int $row, int $column): array =>
                SubGridMapper::subGridValues($gridState, SubGridMapper::coordinatesToSubGridId($row, $column))
        );

        return $grid;
    }
}
