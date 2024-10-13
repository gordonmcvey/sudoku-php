<?php

declare(strict_types=1);

namespace gordonmcvey\sudoku\enum;

enum SubGridIds: int
{
    case TOP_LEFT      = 0;
    case TOP_CENTRE    = 1;
    case TOP_RIGHT     = 2;
    case CENTRE_LEFT   = 3;
    case CENTRE_CENTRE = 4;
    case CENTRE_RIGHT  = 5;
    case BOTTOM_LEFT   = 6;
    case BOTTOM_CENTRE = 7;
    case BOTTOM_RIGHT  = 8;
}
