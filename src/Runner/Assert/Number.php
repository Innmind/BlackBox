<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure\Comparison,
    Assert\Failure\Property,
};

final class Number
{
    private Stats $stats;
    private int|float $number;

    private function __construct(Stats $stats, int|float $number)
    {
        $this->stats = $stats;
        $this->number = $number;
    }

    /**
     * @internal
     */
    public static function of(Stats $stats, int|float $number): self
    {
        return new self($stats, $number);
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function int(string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (!\is_int($this->number)) {
            throw Failure::of(Property::of(
                $this->number,
                $message ?? 'Failed to assert a number is an integer',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function float(string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (!\is_float($this->number)) {
            throw Failure::of(Property::of(
                $this->number,
                $message ?? 'Failed to assert a number is a float',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function greaterThan(int|float $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($this->number <= $actual) {
            throw Failure::of(Comparison::of(
                $this->number,
                $actual,
                $message ?? 'Failed to assert a number is greater than another one',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function greaterThanOrEqual(int|float $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($this->number < $actual) {
            throw Failure::of(Comparison::of(
                $this->number,
                $actual,
                $message ?? 'Failed to assert a number is greater than or equal to another one',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function lessThan(int|float $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($this->number >= $actual) {
            throw Failure::of(Comparison::of(
                $this->number,
                $actual,
                $message ?? 'Failed to assert a number is less than another one',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function lessThanOrEqual(int|float $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($this->number > $actual) {
            throw Failure::of(Comparison::of(
                $this->number,
                $actual,
                $message ?? 'Failed to assert a number is less than or equal to another one',
            ));
        }

        return $this;
    }
}
