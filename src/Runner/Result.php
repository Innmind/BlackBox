<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Result
{
    private function __construct(private bool $successful)
    {
    }

    /**
     * @internal
     */
    public static function of(Stats $stats): self
    {
        return new self($stats->successful());
    }

    #[\NoDiscard]
    public function successful(): bool
    {
        return $this->successful;
    }

    public function exit(): never
    {
        exit(match ($this->successful) {
            true => 0,
            false => 1,
        });
    }
}
