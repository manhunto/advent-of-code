<?php

declare(strict_types=1);

namespace App;

final class Input
{
    public function __construct(
        private readonly string $content,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(implode(PHP_EOL, $data));
    }

    public function asString(): string
    {
        return $this->content;
    }

    public function asArray(): array
    {
        return explode(PHP_EOL, $this->content);
    }

    public function asGrid(): array
    {
        return array_map(static fn (string $row) => str_split($row), $this->asArray());
    }
}
