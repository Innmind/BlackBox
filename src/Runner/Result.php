<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Result
{
    private int $exit;

    private function __construct(bool $successful)
    {
        $this->exit = match ($successful) {
            true => 0,
            false => 1,
        };
    }

    public static function of(Stats $stats): self
    {
        return new self($stats->successful());
    }

    public function exit(): never
    {
        exit($this->exit);
    }
}
