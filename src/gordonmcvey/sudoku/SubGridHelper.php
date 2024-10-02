<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku;

final class SubGridHelper
{
    public const int TOP_LEFT = 0;
    public const int TOP_CENTRE = 1;
    public const int TOP_RIGHT = 2;
    public const int CENTRE_LEFT = 3;
    public const int CENTRE_CENTRE = 4;
    public const int CENTRE_RIGHT = 5;
    public const int BOTTOM_LEFT = 6;
    public const int BOTTOM_CENTRE = 7;
    public const int BOTTOM_RIGHT = 8;

    public const array SUBGRID_IDS = [
        self::TOP_LEFT,
        self::TOP_CENTRE,
        self::TOP_RIGHT,
        self::CENTRE_LEFT,
        self::CENTRE_CENTRE,
        self::CENTRE_RIGHT,
        self::BOTTOM_LEFT,
        self::BOTTOM_CENTRE,
        self::BOTTOM_RIGHT,
    ];

    public const array SUBGRID_CELLS = [
        self::TOP_LEFT      => [
            0 => [0, 1, 2],
            1 => [0, 1, 2],
            2 => [0, 1, 2],
        ],
        self::TOP_CENTRE    => [
            0 => [3, 4, 5],
            1 => [3, 4, 5],
            2 => [3, 4, 5],
        ],
        self::TOP_RIGHT     => [
            0 => [6, 7, 8],
            1 => [6, 7, 8],
            2 => [6, 7, 8],
        ],
        self::CENTRE_LEFT   => [
            3 => [0, 1, 2],
            4 => [0, 1, 2],
            5 => [0, 1, 2],
        ],
        self::CENTRE_CENTRE => [
            3 => [3, 4, 5],
            4 => [3, 4, 5],
            5 => [3, 4, 5],
        ],
        self::CENTRE_RIGHT  => [
            3 => [6, 7, 8],
            4 => [6, 7, 8],
            5 => [6, 7, 8],
        ],
        self::BOTTOM_LEFT   => [
            6 => [0, 1, 2],
            7 => [0, 1, 2],
            8 => [0, 1, 2],
        ],
        self::BOTTOM_CENTRE => [
            6 => [3, 4, 5],
            7 => [3, 4, 5],
            8 => [3, 4, 5],
        ],
        self::BOTTOM_RIGHT  => [
            6 => [6, 7, 8],
            7 => [6, 7, 8],
            8 => [6, 7, 8],
        ],
    ];

    private const array CELL_SUBGRID_MAP = [
        0 => [
            0 => self::TOP_LEFT,
            1 => self::TOP_LEFT,
            2 => self::TOP_LEFT,
            3 => self::TOP_CENTRE,
            4 => self::TOP_CENTRE,
            5 => self::TOP_CENTRE,
            6 => self::TOP_RIGHT,
            7 => self::TOP_RIGHT,
            8 => self::TOP_RIGHT,
        ],
        1 => [
            0 => self::TOP_LEFT,
            1 => self::TOP_LEFT,
            2 => self::TOP_LEFT,
            3 => self::TOP_CENTRE,
            4 => self::TOP_CENTRE,
            5 => self::TOP_CENTRE,
            6 => self::TOP_RIGHT,
            7 => self::TOP_RIGHT,
            8 => self::TOP_RIGHT,
        ],
        2 => [
            0 => self::TOP_LEFT,
            1 => self::TOP_LEFT,
            2 => self::TOP_LEFT,
            3 => self::TOP_CENTRE,
            4 => self::TOP_CENTRE,
            5 => self::TOP_CENTRE,
            6 => self::TOP_RIGHT,
            7 => self::TOP_RIGHT,
            8 => self::TOP_RIGHT,
        ],
        3 => [
            0 => self::CENTRE_LEFT,
            1 => self::CENTRE_LEFT,
            2 => self::CENTRE_LEFT,
            3 => self::CENTRE_CENTRE,
            4 => self::CENTRE_CENTRE,
            5 => self::CENTRE_CENTRE,
            6 => self::CENTRE_RIGHT,
            7 => self::CENTRE_RIGHT,
            8 => self::CENTRE_RIGHT,
        ],
        4 => [
            0 => self::CENTRE_LEFT,
            1 => self::CENTRE_LEFT,
            2 => self::CENTRE_LEFT,
            3 => self::CENTRE_CENTRE,
            4 => self::CENTRE_CENTRE,
            5 => self::CENTRE_CENTRE,
            6 => self::CENTRE_RIGHT,
            7 => self::CENTRE_RIGHT,
            8 => self::CENTRE_RIGHT,
        ],
        5 => [
            0 => self::CENTRE_LEFT,
            1 => self::CENTRE_LEFT,
            2 => self::CENTRE_LEFT,
            3 => self::CENTRE_CENTRE,
            4 => self::CENTRE_CENTRE,
            5 => self::CENTRE_CENTRE,
            6 => self::CENTRE_RIGHT,
            7 => self::CENTRE_RIGHT,
            8 => self::CENTRE_RIGHT,
        ],
        6 => [
            0 => self::BOTTOM_LEFT,
            1 => self::BOTTOM_LEFT,
            2 => self::BOTTOM_LEFT,
            3 => self::BOTTOM_CENTRE,
            4 => self::BOTTOM_CENTRE,
            5 => self::BOTTOM_CENTRE,
            6 => self::BOTTOM_RIGHT,
            7 => self::BOTTOM_RIGHT,
            8 => self::BOTTOM_RIGHT,
        ],
        7 => [
            0 => self::BOTTOM_LEFT,
            1 => self::BOTTOM_LEFT,
            2 => self::BOTTOM_LEFT,
            3 => self::BOTTOM_CENTRE,
            4 => self::BOTTOM_CENTRE,
            5 => self::BOTTOM_CENTRE,
            6 => self::BOTTOM_RIGHT,
            7 => self::BOTTOM_RIGHT,
            8 => self::BOTTOM_RIGHT,
        ],
        8 => [
            0 => self::BOTTOM_LEFT,
            1 => self::BOTTOM_LEFT,
            2 => self::BOTTOM_LEFT,
            3 => self::BOTTOM_CENTRE,
            4 => self::BOTTOM_CENTRE,
            5 => self::BOTTOM_CENTRE,
            6 => self::BOTTOM_RIGHT,
            7 => self::BOTTOM_RIGHT,
            8 => self::BOTTOM_RIGHT,
        ],
    ];

    public function coordinatesToSubgridId(int $row, int $column): int
    {
        return self::CELL_SUBGRID_MAP[$row][$column] ?? throw new \ValueError("Invalid coordinates: $row, $column");
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function cellIdsForSubGrid(int $subGridId): array
    {
        return self::SUBGRID_CELLS[$subGridId] ?? throw new \ValueError("Invalid subgrid ID: $subGridId");
    }
}
