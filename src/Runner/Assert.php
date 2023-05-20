<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Assert
{
    private Stats $stats;

    private function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    public static function of(Stats $stats): self
    {
        return new self($stats);
    }

    /**
     * @param callable(): bool $assertion
     *
     * @throws Failure
     */
    public function that(callable $assertion): void
    {
        if (!$assertion()) {
            throw new Failure;
        }
    }
}
