<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Seed,
    Set\Implementation,
    Exception\EmptySet,
    Random,
};

/**
 * @template T
 * @implements Provider<T>
 */
final class Generator implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<T>): Set<T> $wrap
     * @param callable(Random): \Generator<T|Seed<T>> $factory
     */
    private function __construct(
        private \Closure $wrap,
        private $factory,
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
     * @param callable(Random): \Generator<A|Seed<A>> $factory
     *
     * @return self<A>
     */
    public static function of(
        \Closure $wrap,
        callable $factory,
    ): self {
        /** @var self<A> Don't know why it complains */
        return new self($wrap, $factory, true);
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
            $this->factory,
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
            $this->factory,
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
        return ($this->wrap)(Set\FromGenerator::implementation(
            $this->factory,
            $this->immutable,
        ));
    }
}
