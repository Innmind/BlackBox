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
 * @implements Provider<int>
 */
final class Integers implements Provider
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
     * @return Set<int>
     */
    #[\NoDiscard]
    public function exceptZero(): Set
    {
        return $this->filter(
            static fn(int $value): bool => $value !== 0,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<int<0, max>>
     */
    #[\NoDiscard]
    public function naturalNumbers(): Set
    {
        /** @var Set<0|positive-int> */
        return $this
            ->above(0)
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<int<1, max>>
     */
    #[\NoDiscard]
    public function naturalNumbersExceptZero(): Set
    {
        /** @var Set<int<1, max>> */
        return $this
            ->above(1)
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<int>
     */
    #[\NoDiscard]
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(int): bool $predicate
     *
     * @return Set<int>
     */
    #[\NoDiscard]
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(int): bool $predicate
     *
     * @return Set<int>
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
     * @param callable(int): (V|Seed<V>) $map
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
     * @param callable(Seed<int>): (Set<V>|Provider<V>) $map
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
     * @return Set<int>
     */
    #[\NoDiscard]
    public function randomize(): Set
    {
        return $this->toSet()->randomize();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<?int>
     */
    #[\NoDiscard]
    public function nullable(): Set
    {
        return $this->toSet()->nullable();
    }

    /**
     * @throws EmptySet When no value can be generated
     *
     * @return iterable<int>
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
        return ($this->wrap)(Set\Integers::implementation(
            $this->min,
            $this->max,
        ));
    }
}
