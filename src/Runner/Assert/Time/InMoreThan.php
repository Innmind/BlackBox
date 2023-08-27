<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Time;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure,
    Assert\Failure\Comparison,
};

final class InMoreThan
{
    private Stats $stats;
    /** @var callable(): void */
    private $action;

    /**
     * @param callable(): void $action
     */
    private function __construct(Stats $stats, callable $action)
    {
        $this->stats = $stats;
        $this->action = $action;
    }

    /**
     * @internal
     *
     * @param callable(): void $action
     */
    public static function of(Stats $stats, callable $action): self
    {
        return new self($stats, $action);
    }

    /**
     * @param positive-int $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function milliseconds(int $expected, string $message = null): void
    {
        $this->stats->incrementAssertions();
        /** @var array{0|positive-int, 0|positive-int} */
        $before = \hrtime();

        ($this->action)();

        /** @var array{0|positive-int, 0|positive-int} */
        $after = \hrtime();

        $nanoseconds = $this->diff($before, $after);
        $actual = (int) ($nanoseconds / 1_000_000);

        if ($actual < $expected) {
            throw Failure::of(Comparison::of(
                $expected,
                $actual,
                $message ?? "Function was executed in less than $expected milliseconds",
            ));
        }
    }

    /**
     * @param positive-int $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function seconds(int $expected, string $message = null): void
    {
        $this->stats->incrementAssertions();
        /** @var array{0|positive-int, 0|positive-int} */
        $before = \hrtime();

        ($this->action)();

        /** @var array{0|positive-int, 0|positive-int} */
        $after = \hrtime();

        $nanoseconds = $this->diff($before, $after);
        $actual = (int) ($nanoseconds / 1_000_000_000);

        if ($actual < $expected) {
            throw Failure::of(Comparison::of(
                $expected,
                $actual,
                $message ?? "Function was executed in less than $expected seconds",
            ));
        }
    }

    /**
     * @param array{0|positive-int, 0|positive-int} $before
     * @param array{0|positive-int, 0|positive-int} $after
     *
     * @return 0|positive-int
     */
    private function diff(array $before, array $after): int
    {
        $seconds = $after[0] - $before[0];
        $nanoseconds = match ($seconds) {
            0 => $after[1] - $before[1],
            default => (1_000_000_000 + $after[1]) - $before[1],
        };

        /** @var 0|positive-int */
        return ($seconds * 1_000_000_000) + $nanoseconds;
    }
}
