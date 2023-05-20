<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Stats
{
    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self;
    }

    public function successful(): bool
    {
        return true;
    }
}
