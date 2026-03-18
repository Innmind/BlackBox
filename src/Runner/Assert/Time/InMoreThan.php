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
     * @param int<1, max> $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function milliseconds(int $expected, ?string $message = null): void
    {
        $this->stats->incrementAssertions();
        /** @var array{int<0, max>, int<0, max>} */
        $before = \hrtime();

        ($this->action)();

        /** @var array{int<0, max>, int<0, max>} */
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
     * @param int<1, max> $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function seconds(int $expected, ?string $message = null): void
    {
        $this->stats->incrementAssertions();
        /** @var array{int<0, max>, int<0, max>} */
        $before = \hrtime();

        ($this->action)();

        /** @var array{int<0, max>, int<0, max>} */
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
     * @param array{int<0, max>, int<0, max>} $before
     * @param array{int<0, max>, int<0, max>} $after
     *
     * @return int<0, max>
     */
    private function diff(array $before, array $after): int
    {
        $seconds = $after[0] - $before[0];
        $nanoseconds = match ($seconds) {
            0 => $after[1] - $before[1],
            default => (1_000_000_000 + $after[1]) - $before[1],
        };

        /** @var int<0, max> */
        return ($seconds * 1_000_000_000) + $nanoseconds;
    }
}
