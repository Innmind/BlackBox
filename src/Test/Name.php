<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Test;

use Innmind\BlackBox\Exception\DomainException;
use Innmind\Immutable\Str;

final class Name
{
    private $value;

    public function __construct(string $value)
    {
        if (Str::of($value)->empty()) {
            throw new DomainException;
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
