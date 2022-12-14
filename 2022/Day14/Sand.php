<?php

declare(strict_types=1);

namespace AdventOfCode2022\Day14;

class Sand
{
    public function __construct(
        public int $x,
        public int $y
    ) {
    }

    public function down(): void
    {
        $this->y++;
    }

    public function leftDown(): void
    {
        $this->y++;
        $this->x--;
    }

    public function rightDown(): void
    {
        $this->y++;
        $this->x++;
    }
}
