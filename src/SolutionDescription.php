<?php

declare(strict_types=1);

namespace App;

final class SolutionDescription
{
    public function __construct(
        public readonly Date $date,
        public readonly ?string $name,
        public readonly ?string $href
    ) {
    }
}
