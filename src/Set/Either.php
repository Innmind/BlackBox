<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * @template T
 * @template U
 * @template V
 * @implements Set<T|U|V>
 */
final class Either implements Set
{
    /** @var list<Set<T>|Set<U>|Set<V>> */
    private array $sets;
    private int $size;

    /**
     * @no-named-arguments
     *
     * @param Set<T> $first
     * @param Set<U> $second
     * @param Set<V> $rest
     */
    private function __construct(Set $first, Set $second, Set ...$rest)
    {
        $this->sets = [$first, $second, ...$rest];
        $this->size = 100;
    }

    /**
     * @no-named-arguments
     *
     * @template A
     * @template B
     * @template C
     *
     * @param Set<A> $first
     * @param Set<B> $second
     * @param Set<C> $rest
     *
     * @return self<A, B, C>
     */
    public static function any(Set $first, Set $second, Set ...$rest): self
    {
        return new self($first, $second, ...$rest);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->sets = \array_map(
            static fn(Set $set): Set => $set->take($size),
            $this->sets,
        );
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->sets = \array_map(
            static fn(Set $set): Set => $set->filter($predicate),
            $this->sets,
        );

        return $self;
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        $iterations = 0;
        $emptySets = [];

        while ($iterations < $this->size) {
            $setToChoose = $random->between(0, \count($this->sets) - 1);

            try {
                $value = $this->sets[$setToChoose]->values($random)->current();

                yield $value;
            } catch (EmptySet $e) {
                $emptySets[$setToChoose] = null;

                if (\count($emptySets) === \count($this->sets)) {
                    throw new EmptySet;
                }

                continue;
            }

            ++$iterations;
        }
    }
}
