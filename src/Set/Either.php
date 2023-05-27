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
    /** @var Set<T> */
    private Set $first;
    /** @var Set<U> */
    private Set $second;
    /** @var list<Set<V>> */
    private array $rest;
    /** @var positive-int */
    private int $size;

    /**
     * @no-named-arguments
     *
     * @param positive-int $size
     * @param Set<T> $first
     * @param Set<U> $second
     * @param Set<V> $rest
     */
    private function __construct(int $size, Set $first, Set $second, Set ...$rest)
    {
        $this->first = $first;
        $this->second = $second;
        $this->rest = $rest;
        $this->size = $size;
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
        return new self(100, $first, $second, ...$rest);
    }

    public function take(int $size): Set
    {
        return new self(
            $size,
            $this->first->take($size),
            $this->second->take($size),
            ...\array_map(
                static fn(Set $set): Set => $set->take($size),
                $this->rest,
            ),
        );
    }

    public function filter(callable $predicate): Set
    {
        return new self(
            $this->size,
            $this->first->filter($predicate),
            $this->second->filter($predicate),
            ...\array_map(
                static fn(Set $set): Set => $set->filter($predicate),
                $this->rest,
            ),
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
        $sets = [$this->first, $this->second, ...$this->rest];
        $emptySets = [];

        while ($iterations < $this->size) {
            $setToChoose = $random->between(0, \count($sets) - 1);

            try {
                $value = $sets[$setToChoose]->values($random)->current();

                yield $value;
            } catch (EmptySet $e) {
                // TODO remove the set from the one to choose
                $emptySets[$setToChoose] = null;

                if (\count($emptySets) === \count($sets)) {
                    throw new EmptySet;
                }

                continue;
            }

            ++$iterations;
        }
    }
}
