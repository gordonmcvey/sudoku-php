<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\interface;

interface MutableGridContract
{
    public function fillCoordinates(int $row, int $column, int $value): self;
}
