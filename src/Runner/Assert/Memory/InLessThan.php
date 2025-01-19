<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Memory;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure,
    Assert\Failure\Comparison,
};

final class InLessThan
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
    public function bytes(int $expected, ?string $message = null): void
    {
        $this->assert(
            $expected,
            1,
            $message ?? "Function used more than $expected\B",
        );
    }

    /**
     * @param positive-int $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function kiloBytes(int $expected, ?string $message = null): void
    {
        $this->assert(
            $expected,
            1_000,
            $message ?? "Function used more than $expected\KB",
        );
    }

    /**
     * @param positive-int $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function megaBytes(int $expected, ?string $message = null): void
    {
        $this->assert(
            $expected,
            1_000_000,
            $message ?? "Function used more than $expected\MB",
        );
    }

    /**
     * @param positive-int $expected
     * @param positive-int $power
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
            /** @var 0|positive-int */
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

        if ($actual > ($expected * $power)) {
            throw Failure::of(Comparison::of(
                $expected,
                (int) $actual / $power,
                $message,
            ));
        }
    }
}
