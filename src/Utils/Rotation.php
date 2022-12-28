<?php

declare(strict_types=1);

namespace App\Utils;

enum Rotation: string
{
    case CLOCKWISE = 'CW';
    case ANTICLOCKWISE = 'CCW';
    case TURNABOUT = '180_DEGREE';
}
