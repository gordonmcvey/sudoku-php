<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit;

use gordonmcvey\sudoku\SubGridHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

class SubGridHelperTest extends TestCase
{
    #[Test]
    #[DataProvider("provideCoordinates")]
    public function coordinatesToSubGridId(int $row, int $column, int $expectation): void
    {
        $helper = new SubGridHelper();
        $this->assertSame($expectation, $helper->coordinatesToSubgridId($row, $column));
    }

    /**
     * @return array<string, array{
     *     row: int,
     *     column: int,
     *     expectation: int
     * }
     */
    public static function provideCoordinates(): array
    {
        return [
            "Top left"      => [
                "row"         => 1,
                "column"      => 1,
                "expectation" => SubGridHelper::TOP_LEFT,
            ],
            "Top centre"    => [
                "row"         => 1,
                "column"      => 4,
                "expectation" => SubGridHelper::TOP_CENTRE,
            ],
            "Top right"     => [
                "row"         => 1,
                "column"      => 7,
                "expectation" => SubGridHelper::TOP_RIGHT,
            ],
            "Centre left"   => [
                "row"         => 4,
                "column"      => 1,
                "expectation" => SubGridHelper::CENTRE_LEFT,
            ],
            "Centre centre" => [
                "row"         => 4,
                "column"      => 4,
                "expectation" => SubGridHelper::CENTRE_CENTRE,
            ],
            "Centre right"  => [
                "row"         => 4,
                "column"      => 7,
                "expectation" => SubGridHelper::CENTRE_RIGHT,
            ],
            "Bottom left"   => [
                "row"         => 7,
                "column"      => 1,
                "expectation" => SubGridHelper::BOTTOM_LEFT,
            ],
            "Bottom centre" => [
                "row"         => 7,
                "column"      => 4,
                "expectation" => SubGridHelper::BOTTOM_CENTRE,
            ],
            "Bottom right"  => [
                "row"         => 7,
                "column"      => 7,
                "expectation" => SubGridHelper::BOTTOM_RIGHT,
            ],
        ];
    }

    #[Test]
    #[DataProvider("provideCoordinatesInvalid")]
    public function coordinatesToSubGridIdInvalid(int $row, int $column): void
    {
        $helper = new SubGridHelper();
        $this->expectException(ValueError::class);
        $helper->coordinatesToSubgridId($row, $column);
    }

    /**
     * @return array<string, array{
     *     row: int,
     *     column: int
     * }
     */
    public static function provideCoordinatesInvalid(): array
    {
        return [
            "Row below minimum"    => [
                "row"         => -1,
                "column"      => 1,
            ],
            "Row above maximum"    => [
                "row"         => 9,
                "column"      => 4,
            ],
            "Column below minimum" => [
                "row"         => 1,
                "column"      => -1,
            ],
            "Column above maximum" => [
                "row"         => 4,
                "column"      => 9,
            ],
        ];
    }

    #[Test]
    public function cellIdsForSubGrid(): void
    {
        $helper = new SubGridHelper();
        $this->assertSame([
            3 => [
                0 => 3,
                1 => 4,
                2 => 5,
            ],
            4 => [
                0 => 3,
                1 => 4,
                2 => 5,
            ],
            5 => [
                0 => 3,
                1 => 4,
                2 => 5,
            ],
        ], $helper->cellIdsForSubGrid(SubGridHelper::CENTRE_CENTRE));
    }

    #[Test]
    public function cellForSubGridInvalid(): void
    {
        $helper = new SubGridHelper();
        $this->expectException(ValueError::class);
        $helper->cellIdsForSubGrid(10);
    }
}
