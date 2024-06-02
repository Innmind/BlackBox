<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure\Property,
    Assert\Failure\Comparison,
};

final class Not
{
    private Stats $stats;

    private function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    /**
     * @internal
     */
    public static function of(Stats $stats): self
    {
        return new self($stats);
    }

    /**
     * @template R
     *
     * @param callable(): R $attempt
     * @param non-empty-string $message
     *
     * @throws Failure
     *
     * @return R
     */
    public function throws(
        callable $attempt,
        string $message = null,
    ): mixed {
        $this->stats->incrementAssertions();

        try {
            return $attempt();
        } catch (\Throwable $e) {
            throw Failure::of(Property::of(
                $e,
                $message ?? 'Failed asserting that a callable does not throw an exception',
            ));
        }
    }

    /**
     * @param 0|positive-int $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function count(
        int $expected,
        \Countable|array $collection,
        string $message = null,
    ): self {
        $this->stats->incrementAssertions();

        $count = \count($collection);

        if ($count === $expected) {
            throw Failure::of(Comparison::of(
                $expected,
                $count,
                $message ?? \sprintf(
                    'Failed to assert that a collection does not contain %s element(s)',
                    $expected,
                ),
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function true(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($actual === true) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is not true',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function false(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($actual === false) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is not false',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function bool(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (\is_bool($actual)) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is not a boolean',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function null(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($actual === null) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is not null',
            ));
        }

        return $this;
    }
}
