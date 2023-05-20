<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure\Comparison,
};

final class Expected
{
    private Stats $stats;
    private mixed $value;

    private function __construct(Stats $stats, mixed $value)
    {
        $this->stats = $stats;
        $this->value = $value;
    }

    public static function of(Stats $stats, mixed $value): self
    {
        return new self($stats, $value);
    }

    public function not(): Expected\Not
    {
        return Expected\Not::of($this->stats, $this->value);
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function same(mixed $value, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($this->value !== $value) {
            throw Failure::of(Comparison::of(
                $this->value,
                $value,
                $message ?? 'Failed to assert two variables are the same',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function equals(mixed $value, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($this->value != $value) {
            throw Failure::of(Comparison::of(
                $this->value,
                $value,
                $message ?? 'Failed to assert two variables are equal',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function in(iterable $collection, string $message = null): self
    {
        $this->stats->incrementAssertions();

        /** @var mixed $value */
        foreach ($collection as $value) {
            if ($this->value === $value) {
                return $this;
            }
        }

        throw Failure::of(Comparison::of(
            $this->value,
            $collection,
            $message ?? 'Failed to assert a variable is contained in an iterable',
        ));
    }
}
