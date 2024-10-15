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

    #[Test]
    #[DataProvider("provideSubGrids")]
    public function subGridValues(array $gridState, SubGridIds $subGridId, array $expectation): void
    {
        $this->assertSame($expectation, SubGridMapper::subGridValues($gridState, $subGridId));
    }

    public static function provideSubGrids(): array
    {
        return [
            "Complete Top Left"     => [
                "gridState"   => [
                    0 => [0 => 1, 1 => 2, 2 => 3],
                    1 => [0 => 5, 1 => 8, 2 => 4],
                    2 => [0 => 9, 1 => 6, 2 => 7],
                ],
                "subGridId"   => SubGridIds::TOP_LEFT,
                "expectation" => [1, 2, 3, 5, 8, 4, 9, 6, 7],
            ],
            "Complete Centre"       => [
                "gridState"   => [
                    3 => [3 => 4, 4 => 6, 5 => 1],
                    4 => [3 => 5, 4 => 8, 5 => 3],
                    5 => [3 => 7, 4 => 9, 5 => 2],
                ],
                "subGridId"   => SubGridIds::CENTRE_CENTRE,
                "expectation" => [4, 6, 1, 5, 8, 3, 7, 9, 2],
            ],
            "Complete Bottom Right" => [
                "gridState"   => [
                    6 => [6 => 1, 7 => 5, 8 => 7],
                    7 => [6 => 4, 7 => 3, 8 => 6],
                    8 => [6 => 8, 7 => 9, 8 => 2],
                ],
                "subGridId"   => SubGridIds::BOTTOM_RIGHT,
                "expectation" => [1, 5, 7, 4, 3, 6, 8, 9, 2],
            ],
            "Partial Top Centre"    => [
                "gridState"   => [
                    0 => [3 => 6],
                    1 => [4 => 3],
                    2 => [5 => 5],
                ],
                "subGridId"   => SubGridIds::TOP_CENTRE,
                "expectation" => [6, 3, 5],
            ],
            "Empty Bottom Centre"   => [
                "gridState"   => [],
                "subGridId"   => SubGridIds::BOTTOM_CENTRE,
                "expectation" => [],
            ],
        ];
    }
}
