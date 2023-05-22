<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

final class Name
{
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function of(string $name): self
    {
        return new self($name);
    }

    public function toString(): string
    {
        return $this->name;
    }
}
