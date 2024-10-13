<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit\util;

use gordonmcvey\sudoku\enum\SubGridIds;
use gordonmcvey\sudoku\util\SubGridMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ValueError;

class SubGridMapperTest extends TestCase
{
    #[Test]
    #[DataProvider("provideCoordinates")]
    public function coordinatesToSubGridId(int $row, int $column, SubGridIds $expectation): void
    {
        $this->assertSame($expectation, SubGridMapper::coordinatesToSubgridId($row, $column));
    }

    /**
     * @return array<string, array{
     *     row: int,
     *     column: int,
     *     expectation: SubGridIds
     * }
     */
    public static function provideCoordinates(): array
    {
        return [
            "Top left"      => [
                "row"         => 1,
                "column"      => 1,
                "expectation" => SubGridIds::TOP_LEFT,
            ],
            "Top centre"    => [
                "row"         => 1,
                "column"      => 4,
                "expectation" => SubGridIds::TOP_CENTRE,
            ],
            "Top right"     => [
                "row"         => 1,
                "column"      => 7,
                "expectation" => SubGridIds::TOP_RIGHT,
            ],
            "Centre left"   => [
                "row"         => 4,
                "column"      => 1,
                "expectation" => SubGridIds::CENTRE_LEFT,
            ],
            "Centre centre" => [
                "row"         => 4,
                "column"      => 4,
                "expectation" => SubGridIds::CENTRE_CENTRE,
            ],
            "Centre right"  => [
                "row"         => 4,
                "column"      => 7,
                "expectation" => SubGridIds::CENTRE_RIGHT,
            ],
            "Bottom left"   => [
                "row"         => 7,
                "column"      => 1,
                "expectation" => SubGridIds::BOTTOM_LEFT,
            ],
            "Bottom centre" => [
                "row"         => 7,
                "column"      => 4,
                "expectation" => SubGridIds::BOTTOM_CENTRE,
            ],
            "Bottom right"  => [
                "row"         => 7,
                "column"      => 7,
                "expectation" => SubGridIds::BOTTOM_RIGHT,
            ],
        ];
    }

    #[Test]
    #[DataProvider("provideCoordinatesInvalid")]
    public function coordinatesToSubGridIdInvalid(int $row, int $column): void
    {
        $this->expectException(ValueError::class);
        SubGridMapper::coordinatesToSubgridId($row, $column);
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
        ], SubGridMapper::cellIdsForSubGrid(SubGridIds::CENTRE_CENTRE));
    }
}
