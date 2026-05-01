<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Memory;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure,
    Assert\Failure\Comparison,
};

final class InMoreThan
{
    /**
     * @param \Closure(): void $action
     */
    private function __construct(
        private Stats $stats,
        private \Closure $action,
    ) {
    }

    /**
     * @internal
     *
     * @param \Closure(): void $action
     */
    public static function of(Stats $stats, \Closure $action): self
    {
        return new self($stats, $action);
    }

    /**
     * @param int<1, max> $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function bytes(int $expected, ?string $message = null): void
    {
        $this->assert(
            $expected,
            1,
            $message ?? "Function used less than $expected\B",
        );
    }

    /**
     * @param int<1, max> $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function kiloBytes(int $expected, ?string $message = null): void
    {
        $this->assert(
            $expected,
            1_000,
            $message ?? "Function used less than $expected\KB",
        );
    }

    /**
     * @param int<1, max> $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function megaBytes(int $expected, ?string $message = null): void
    {
        $this->assert(
            $expected,
            1_000_000,
            $message ?? "Function used less than $expected\MB",
        );
    }

    /**
     * @param int<1, max> $expected
     * @param int<1, max> $power
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    private function assert(int $expected, int $power, string $message): void
    {
        $this->stats->incrementAssertions();
        $before = \memory_get_usage();
        $max = \memory_get_usage();
        $inspect = static function() use (&$max): void {
            /** @var int<0, max> */
            $max = \max($max, \memory_get_usage());
        };
        \register_tick_function($inspect);

        ($this->action)();

        \unregister_tick_function($inspect);
        /**
         * @psalm-suppress MixedAssignment
         * @psalm-suppress MixedOperand
         */
        $actual = $max - $before;

        if ($actual < ($expected * $power)) {
            throw Failure::of(Comparison::of(
                $expected,
                (int) $actual / $power,
                $message,
            ));
        }
    }
}
