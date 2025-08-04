<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Result
{
    private bool $successful;
    private int $exit;

    private function __construct(bool $successful)
    {
        $this->successful = $successful;
        $this->exit = match ($successful) {
            true => 0,
            false => 1,
        };
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
        exit($this->exit);
    }
}
