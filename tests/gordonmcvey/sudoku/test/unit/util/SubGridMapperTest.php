<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\unit\util;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use gordonmcvey\sudoku\enum\SubGridIds;
use gordonmcvey\sudoku\util\SubGridMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SubGridMapperTest extends TestCase
{
    #[Test]
    #[DataProvider("provideCoordinates")]
    public function coordinatesToSubGridId(RowIds $row, ColumnIds $column, SubGridIds $expectation): void
    {
        $this->assertSame($expectation, SubGridMapper::coordinatesToSubgridId($row, $column));
    }

    /**
     * @return array<string, array{
     *     row: RowIds,
     *     column: ColumnIds,
     *     expectation: SubGridIds
     * }
     */
    public static function provideCoordinates(): array
    {
        return [
            "Top left"      => [
                "row"         => RowIds::ROW_2,
                "column"      => ColumnIds::COL_2,
                "expectation" => SubGridIds::TOP_LEFT,
            ],
            "Top centre"    => [
                "row"         => RowIds::ROW_2,
                "column"      => ColumnIds::COL_5,
                "expectation" => SubGridIds::TOP_CENTRE,
            ],
            "Top right"     => [
                "row"         => RowIds::ROW_2,
                "column"      => ColumnIds::COL_8,
                "expectation" => SubGridIds::TOP_RIGHT,
            ],
            "Centre left"   => [
                "row"         => RowIds::ROW_5,
                "column"      => ColumnIds::COL_2,
                "expectation" => SubGridIds::CENTRE_LEFT,
            ],
            "Centre centre" => [
                "row"         => RowIds::ROW_5,
                "column"      => ColumnIds::COL_5,
                "expectation" => SubGridIds::CENTRE_CENTRE,
            ],
            "Centre right"  => [
                "row"         => RowIds::ROW_5,
                "column"      => ColumnIds::COL_8,
                "expectation" => SubGridIds::CENTRE_RIGHT,
            ],
            "Bottom left"   => [
                "row"         => RowIds::ROW_8,
                "column"      => ColumnIds::COL_2,
                "expectation" => SubGridIds::BOTTOM_LEFT,
            ],
            "Bottom centre" => [
                "row"         => RowIds::ROW_8,
                "column"      => ColumnIds::COL_5,
                "expectation" => SubGridIds::BOTTOM_CENTRE,
            ],
            "Bottom right"  => [
                "row"         => RowIds::ROW_8,
                "column"      => ColumnIds::COL_8,
                "expectation" => SubGridIds::BOTTOM_RIGHT,
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
