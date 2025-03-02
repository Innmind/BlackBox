<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Implementation,
    Set\Integers,
};

/**
 * @template V
 * @implements Provider<list<V>>
 */
final class Sequence implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<list<V>>): Set<list<V>> $wrap
     * @param Implementation<V> $set
     */
    private function __construct(
        private \Closure $wrap,
        private Implementation $set,
        private Integers $sizes,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template A
     *
     * @param pure-Closure(Implementation<list<A>>): Set<list<A>> $wrap
     * @param Implementation<A> $set
     *
     * @return self<A>
     */
    public static function of(
        \Closure $wrap,
        Implementation $set,
    ): self {
        return new self($wrap, $set, Integers::implementation(0, 100));
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<0, max> $min
     * @param int<1, max> $max
     *
     * @return self<V>
     */
    public function between(int $min, int $max): self
    {
        return new self(
            $this->wrap,
            $this->set,
            Integers::implementation($min, $max),
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return self<V>
     */
    public function atLeast(int $size): self
    {
        return new self(
            $this->wrap,
            $this->set,
            Integers::implementation($size, $size + 100),
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return self<V>
     */
    public function atMost(int $size): self
    {
        return new self(
            $this->wrap,
            $this->set,
            Integers::implementation(0, $size),
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<list<V>>
     */
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(list<V>): bool $predicate
     *
     * @return Set<list<V>>
     */
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template U
     *
     * @param callable(list<V>): U $map
     *
     * @return Set<U>
     */
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return ($this->wrap)(Set\Sequence::implementation(
            $this->set,
            $this->sizes,
        ));
    }
}
