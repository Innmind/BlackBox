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
 * @template T
 * @implements Provider<T>
 */
final class Composite implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<T>): Set<T> $wrap
     * @param callable(...mixed): (T|Seed<T>) $aggregate
     * @param list<Implementation> $rest
     */
    private function __construct(
        private \Closure $wrap,
        private $aggregate,
        private Implementation $first,
        private Implementation $second,
        private array $rest,
        private bool $immutable,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     * @no-named-arguments
     *
     * @param pure-Closure(Implementation<A>): Set<A> $wrap
     * @param callable(...mixed): (A|Seed<A>) $aggregate
     *
     * @return self<A>
     */
    public static function of(
        \Closure $wrap,
        callable $aggregate,
        Implementation $first,
        Implementation $second,
        Implementation ...$rest,
    ): self {
        return new self($wrap, $aggregate, $first, $second, $rest, true);
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    #[\NoDiscard]
    public function immutable(): self
    {
        return new self(
            $this->wrap,
            $this->aggregate,
            $this->first,
            $this->second,
            $this->rest,
            true,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    #[\NoDiscard]
    public function mutable(): self
    {
        return new self(
            $this->wrap,
            $this->aggregate,
            $this->first,
            $this->second,
            $this->rest,
            false,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<T>
     */
    #[\NoDiscard]
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): bool $predicate
     *
     * @return Set<T>
     */
    #[\NoDiscard]
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): bool $predicate
     *
     * @return Set<T>
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
     * @param callable(T): (V|Seed<V>) $map
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
     * @param callable(Seed<T>): (Set<V>|Provider<V>) $map
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
     * @return Set<T>
     */
    #[\NoDiscard]
    public function randomize(): Set
    {
        return $this->toSet()->randomize();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<?T>
     */
    #[\NoDiscard]
    public function nullable(): Set
    {
        return $this->toSet()->nullable();
    }

    /**
     * @throws EmptySet When no value can be generated
     *
     * @return iterable<T>
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
        return ($this->wrap)(Set\Composite::implementation(
            $this->immutable,
            $this->aggregate,
            $this->first,
            $this->second,
            ...$this->rest,
        ));
    }
}
