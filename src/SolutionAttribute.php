<?php

declare(strict_types=1);

namespace App;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class SolutionAttribute
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
