<?php

declare(strict_types=1);

namespace App;

enum InputType: string
{
    case Puzzle = 'puzzle';
    case Example = 'example';
}
