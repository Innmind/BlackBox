<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Runner\Assert\{
    Failure,
    Failure\Truth,
};

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
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function that(
        callable $assertion,
        string $message = null,
    ): void {
        $this->stats->incrementAssertions();

        if (!$assertion()) {
            throw Failure::of(Truth::of($message ?? 'Failed to verify that an assertion is true'));
        }
    }

    /**
     * @throws Failure
     */
    public function same(mixed $a, mixed $b): void
    {
        $this->that(static fn() => $a === $b);
    }
}
