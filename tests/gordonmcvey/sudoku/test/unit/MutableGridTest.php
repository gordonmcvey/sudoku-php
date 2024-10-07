<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit;

use gordonmcvey\sudoku\exception\InvalidColumnUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidRowUniqueConstraintException;
use gordonmcvey\sudoku\exception\InvalidSubGridUniqueConstraintException;
use gordonmcvey\sudoku\MutableGrid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Random\Randomizer;
use Throwable;

class MutableGridTest extends TestCase
{
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
        $grid = new MutableGrid();

        foreach ($entries as $entry) {
            $grid->fillCoordinates($entry["row"], $entry["column"], $entry["value"]);
        }

        $this->assertSame($expectation, $grid->grid());
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

    /**
     * @param array<array-key, array{
     *     row: int,
     *     column: int,
     *     value: int
     * }> $entries,
     * @param class-string<Throwable> $expectedException
     */
    #[Test]
    #[DataProvider("provideForFillCoordinatesViolatesUniqueConstraints")]
    public function fillCoordinatesViolatesUniqueConstraints(array $entries, string $expectedException): void
    {
        $grid = new MutableGrid();

        $this->expectException($expectedException);
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
     *     }>,
     *     expectedException: class-string<Throwable>
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
                "expectedException" => InvalidRowUniqueConstraintException::class,
            ],
            "Violates column constraint" => [
                "entries" => [
                    ["row" => 0, "column" => 0, "value" => 2],
                    ["row" => 1, "column" => 0, "value" => 2],
                ],
                "expectedException" => InvalidColumnUniqueConstraintException::class,
            ],
            "Violates subgrid constraint" => [
                "entries" => [
                    ["row" => 0, "column" => 0, "value" => 3],
                    ["row" => 1, "column" => 1, "value" => 3],
                ],
                "expectedException" => InvalidSubgridUniqueConstraintException::class,
            ],
        ];
    }

    #[Test]
    public function fillCoordinatesDoesNotMutateForInvalidInput(): void
    {
        $expected = [0 => [0 => 1]];
        $grid = new MutableGrid();

        $grid->fillCoordinates(0, 0, 1);
        $this->assertSame($expected, $grid->grid());

        try {
            $grid->fillCoordinates(1, 0, 1);
        } catch (Throwable $e) {
        }

        $this->assertSame($expected, $grid->grid());
    }
}
