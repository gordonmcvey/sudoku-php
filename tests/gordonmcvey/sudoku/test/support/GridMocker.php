<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\test\support;

use gordonmcvey\sudoku\enum\ColumnIds;
use gordonmcvey\sudoku\enum\RowIds;
use gordonmcvey\sudoku\interface\GridContract;
use gordonmcvey\sudoku\util\SubGridMapper;
use PHPUnit\Framework\MockObject\MockObject;

class GridMocker
{
    public static function configure(GridContract&MockObject $grid, array $puzzle): GridContract&MockObject
    {
        $grid->method("grid")->willReturn($puzzle);

        $grid->method("cellAtCoordinates")
            ->willReturnCallback(fn (RowIds $row, ColumnIds $column): ?int => $puzzle[$row->value][$column->value] ?? null);

        $grid->method("row")
            ->willReturnCallback(fn (RowIds $row): array => $puzzle[$row->value] ?? []);

        $grid->method("column")
            ->willReturnCallback(fn (ColumnIds $column): array => array_column($puzzle, $column->value));

        // This is a bit of a cheat because we're using the SubGridMapper to implement this method for the mock, but
        // if we didn't, we'd basically have to re-implement the same logic for the mock.  I feel that this is an
        // acceptable break from the normal rules of unit tests.
        $grid->method("subGridAtCoordinates")->willReturnCallback(
            fn (RowIds $row, ColumnIds $column): array =>
            SubGridMapper::subGridValues($puzzle, SubGridMapper::coordinatesToSubGridId($row, $column))
        );

        return $grid;
    }
}
