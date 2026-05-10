<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Seed,
    Set\Implementation,
    Exception\EmptySet,
};

/**
 * @implements Provider<float>
 */
final class RealNumbers implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<int>): Set<int> $wrap
     */
    private function __construct(
        private \Closure $wrap,
        private ?int $min,
        private ?int $max,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param pure-Closure(Implementation<int>): Set<int> $wrap
     */
    public static function of(\Closure $wrap): self
    {
        return new self($wrap, null, null);
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
    public function between(int $min, int $max): self
    {
        return new self($this->wrap, $min, $max);
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
    public function above(int $min): self
    {
        return new self($this->wrap, $min, null);
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
    public function below(int $max): self
    {
        return new self($this->wrap, null, $max);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<float>
     */
    #[\NoDiscard]
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(float): bool $predicate
     *
     * @return Set<float>
     */
    #[\NoDiscard]
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(float): bool $predicate
     *
     * @return Set<float>
     */
    #[\NoDiscard]
    public function exclude(callable $predicate): Set
    {
        return $this->toSet()->exclude($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(float): (V|Seed<V>) $map
     *
     * @return Set<V>
     */
    #[\NoDiscard]
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(Seed<float>): (Set<V>|Provider<V>) $map
     *
     * @return Set<V>
     */
    #[\NoDiscard]
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @template R
     *
     * @param Set<R>|Provider<R> $right
     *
     * @return Set<array{float, R}>
     */
    #[\NoDiscard]
    public function zip(Set|Provider $right): Set
    {
        return $this->toSet()->zip($right);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<float>
     */
    #[\NoDiscard]
    public function randomize(): Set
    {
        return $this->toSet()->randomize();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<?float>
     */
    #[\NoDiscard]
    public function nullable(): Set
    {
        return $this->toSet()->nullable();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<float>
     */
    #[\NoDiscard]
    public function disableShrinking(): Set
    {
        return $this->toSet()->disableShrinking();
    }

    /**
     * @throws EmptySet When no value can be generated
     *
     * @return iterable<float>
     */
    #[\NoDiscard]
    public function enumerate(): iterable
    {
        return $this->toSet()->enumerate();
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    #[\NoDiscard]
    public function toSet(): Set
    {
        $min = $this->min;
        $max = $this->max;
        $decimals = Set::integers()
            ->above(1)
            ->map(static fn($fraction) => 1 / $fraction);

        /** @psalm-suppress InvalidOperand */
        $set = ($this->wrap)(Set\Integers::implementation(
            $min,
            $max,
        ))
            ->exclude(static fn($int) => $int === 0)
            ->zip($decimals)
            ->map(static fn($pair) => match ($pair[0] <=> 0) {
                0 => $pair[1],
                1 => $pair[0] - $pair[1],
                -1 => $pair[0] + $pair[1],
            });

        if (!\is_null($min)) {
            $set = $set->exclude(static fn($value) => $value < $min);
        }

        if (!\is_null($max)) {
            $set = $set->exclude(static fn($value) => $value > $max);
        }

        return $set;
    }
}
