<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\util;

use gordonmcvey\sudoku\enum\SubGridIds;
use ValueError;

/**
 * Mapper class for identifying subgrids in a Sudoku puzzle
 *
 * A Sudoku is a grid of 9x9 cells with rows and columns relevant to the game, but also sub-grids (3 x 3 cells).  In
 * this library, subgrids are identified as follows (imagine the below to be a sudoku grid, with each numbered section
 * representing a subgrid):
 *
 *  0 | 1 | 2
 * ---+---+---
 *  3 | 4 | 5
 * ---+---+---
 *  6 | 7 | 8
 */
class SubGridMapper
{
    private const array SUBGRID_CELLS = [
        SubGridIds::TOP_LEFT->value      => [
            0 => [0, 1, 2],
            1 => [0, 1, 2],
            2 => [0, 1, 2],
        ],
        SubGridIds::TOP_CENTRE->value    => [
            0 => [3, 4, 5],
            1 => [3, 4, 5],
            2 => [3, 4, 5],
        ],
        SubGridIds::TOP_RIGHT->value     => [
            0 => [6, 7, 8],
            1 => [6, 7, 8],
            2 => [6, 7, 8],
        ],
        SubGridIds::CENTRE_LEFT->value   => [
            3 => [0, 1, 2],
            4 => [0, 1, 2],
            5 => [0, 1, 2],
        ],
        SubGridIds::CENTRE_CENTRE->value => [
            3 => [3, 4, 5],
            4 => [3, 4, 5],
            5 => [3, 4, 5],
        ],
        SubGridIds::CENTRE_RIGHT->value  => [
            3 => [6, 7, 8],
            4 => [6, 7, 8],
            5 => [6, 7, 8],
        ],
        SubGridIds::BOTTOM_LEFT->value   => [
            6 => [0, 1, 2],
            7 => [0, 1, 2],
            8 => [0, 1, 2],
        ],
        SubGridIds::BOTTOM_CENTRE->value => [
            6 => [3, 4, 5],
            7 => [3, 4, 5],
            8 => [3, 4, 5],
        ],
        SubGridIds::BOTTOM_RIGHT->value  => [
            6 => [6, 7, 8],
            7 => [6, 7, 8],
            8 => [6, 7, 8],
        ],
    ];

    private const array CELL_SUBGRID_MAP = [
        0 => [
            0 => SubGridIds::TOP_LEFT,
            1 => SubGridIds::TOP_LEFT,
            2 => SubGridIds::TOP_LEFT,
            3 => SubGridIds::TOP_CENTRE,
            4 => SubGridIds::TOP_CENTRE,
            5 => SubGridIds::TOP_CENTRE,
            6 => SubGridIds::TOP_RIGHT,
            7 => SubGridIds::TOP_RIGHT,
            8 => SubGridIds::TOP_RIGHT,
        ],
        1 => [
            0 => SubGridIds::TOP_LEFT,
            1 => SubGridIds::TOP_LEFT,
            2 => SubGridIds::TOP_LEFT,
            3 => SubGridIds::TOP_CENTRE,
            4 => SubGridIds::TOP_CENTRE,
            5 => SubGridIds::TOP_CENTRE,
            6 => SubGridIds::TOP_RIGHT,
            7 => SubGridIds::TOP_RIGHT,
            8 => SubGridIds::TOP_RIGHT,
        ],
        2 => [
            0 => SubGridIds::TOP_LEFT,
            1 => SubGridIds::TOP_LEFT,
            2 => SubGridIds::TOP_LEFT,
            3 => SubGridIds::TOP_CENTRE,
            4 => SubGridIds::TOP_CENTRE,
            5 => SubGridIds::TOP_CENTRE,
            6 => SubGridIds::TOP_RIGHT,
            7 => SubGridIds::TOP_RIGHT,
            8 => SubGridIds::TOP_RIGHT,
        ],
        3 => [
            0 => SubGridIds::CENTRE_LEFT,
            1 => SubGridIds::CENTRE_LEFT,
            2 => SubGridIds::CENTRE_LEFT,
            3 => SubGridIds::CENTRE_CENTRE,
            4 => SubGridIds::CENTRE_CENTRE,
            5 => SubGridIds::CENTRE_CENTRE,
            6 => SubGridIds::CENTRE_RIGHT,
            7 => SubGridIds::CENTRE_RIGHT,
            8 => SubGridIds::CENTRE_RIGHT,
        ],
        4 => [
            0 => SubGridIds::CENTRE_LEFT,
            1 => SubGridIds::CENTRE_LEFT,
            2 => SubGridIds::CENTRE_LEFT,
            3 => SubGridIds::CENTRE_CENTRE,
            4 => SubGridIds::CENTRE_CENTRE,
            5 => SubGridIds::CENTRE_CENTRE,
            6 => SubGridIds::CENTRE_RIGHT,
            7 => SubGridIds::CENTRE_RIGHT,
            8 => SubGridIds::CENTRE_RIGHT,
        ],
        5 => [
            0 => SubGridIds::CENTRE_LEFT,
            1 => SubGridIds::CENTRE_LEFT,
            2 => SubGridIds::CENTRE_LEFT,
            3 => SubGridIds::CENTRE_CENTRE,
            4 => SubGridIds::CENTRE_CENTRE,
            5 => SubGridIds::CENTRE_CENTRE,
            6 => SubGridIds::CENTRE_RIGHT,
            7 => SubGridIds::CENTRE_RIGHT,
            8 => SubGridIds::CENTRE_RIGHT,
        ],
        6 => [
            0 => SubGridIds::BOTTOM_LEFT,
            1 => SubGridIds::BOTTOM_LEFT,
            2 => SubGridIds::BOTTOM_LEFT,
            3 => SubGridIds::BOTTOM_CENTRE,
            4 => SubGridIds::BOTTOM_CENTRE,
            5 => SubGridIds::BOTTOM_CENTRE,
            6 => SubGridIds::BOTTOM_RIGHT,
            7 => SubGridIds::BOTTOM_RIGHT,
            8 => SubGridIds::BOTTOM_RIGHT,
        ],
        7 => [
            0 => SubGridIds::BOTTOM_LEFT,
            1 => SubGridIds::BOTTOM_LEFT,
            2 => SubGridIds::BOTTOM_LEFT,
            3 => SubGridIds::BOTTOM_CENTRE,
            4 => SubGridIds::BOTTOM_CENTRE,
            5 => SubGridIds::BOTTOM_CENTRE,
            6 => SubGridIds::BOTTOM_RIGHT,
            7 => SubGridIds::BOTTOM_RIGHT,
            8 => SubGridIds::BOTTOM_RIGHT,
        ],
        8 => [
            0 => SubGridIds::BOTTOM_LEFT,
            1 => SubGridIds::BOTTOM_LEFT,
            2 => SubGridIds::BOTTOM_LEFT,
            3 => SubGridIds::BOTTOM_CENTRE,
            4 => SubGridIds::BOTTOM_CENTRE,
            5 => SubGridIds::BOTTOM_CENTRE,
            6 => SubGridIds::BOTTOM_RIGHT,
            7 => SubGridIds::BOTTOM_RIGHT,
            8 => SubGridIds::BOTTOM_RIGHT,
        ],
    ];

    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }

    public static function coordinatesToSubGridId(int $row, int $column): SubGridIds
    {
        return self::CELL_SUBGRID_MAP[$row][$column] ?? throw new ValueError("Invalid coordinates: $row, $column");
    }

    /**
     * @return array<int, array<int, int>>
     */
    public static function cellIdsForSubGrid(SubGridIds $subGridId): array
    {
        return self::SUBGRID_CELLS[$subGridId->value];
    }
}
