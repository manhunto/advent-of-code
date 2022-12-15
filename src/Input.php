<?php

declare(strict_types=1);

namespace App;

final class Input
{
    public function __construct(
        private readonly string $content,
        public readonly InputType $inputType,
    ) {
    }

    public static function fromArray(array $data, InputType $inputType): self
    {
        return new self(implode(PHP_EOL, $data), $inputType);
    }

    public function asString(): string
    {
        return $this->content;
    }

    public function asArray(): array
    {
        return explode(PHP_EOL, $this->content);
    }

    /**
     * Removes empty lines inside input
     */
    public function asArrayWithoutEmptyLines(): array
    {
        return array_values(array_filter($this->asArray()));
    }

    public function asGrid(): array
    {
        return array_map(static fn (string $row) => str_split($row), $this->asArray());
    }
}
