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
 * @implements Implementation<T|U|V>
 */
final class Either implements Implementation
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
     * @psalm-mutation-free
     *
     * @no-named-arguments
     *
     * @param positive-int $size
     * @param Set<T>|Provider<T> $first
     * @param Set<U>|Provider<U> $second
     * @param Set<V>|Provider<V> $rest
     */
    private function __construct(
        int $size,
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$rest,
    ) {
        $this->first = Collapse::of($first);
        $this->second = Collapse::of($second);
        /** @psalm-suppress PossiblyInvalidArgument */
        $this->rest = \array_map(Collapse::of(...), $rest);
        $this->size = $size;
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @template A
     * @template B
     * @template C
     *
     * @param Set<A>|Provider<A> $first
     * @param Set<B>|Provider<B> $second
     * @param Set<C>|Provider<C> $rest
     *
     * @return self<A, B, C>
     */
    public static function implementation(
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$rest,
    ): self {
        return new self(100, $first, $second, ...$rest);
    }

    /**
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @template A
     * @template B
     * @template C
     *
     * @param Set<A>|Provider<A> $first
     * @param Set<B>|Provider<B> $second
     * @param Set<C>|Provider<C> $rest
     *
     * @return Set<A|B|C>
     */
    public static function any(
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$rest,
    ): Set {
        return Set::either($first, $second, ...$rest);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
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

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): self
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
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Implementation
    {
        return Decorate::implementation($map, $this);
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $iterations = 0;
        /** @var list<Set<T>|Set<U>|Set<V>> */
        $sets = [$this->first, $this->second, ...$this->rest];

        while ($iterations < $this->size) {
            $count = \count($sets);

            if ($count === 0 && $iterations === 0) {
                throw new EmptySet;
            }

            if ($count === 0) {
                return;
            }

            $setToChoose = $random->between(0, $count - 1);

            try {
                $value = $sets[$setToChoose]->values($random)->current();

                if (\is_null($value)) {
                    continue;
                }

                yield $value;
            } catch (EmptySet $e) {
                unset($sets[$setToChoose]);
                $sets = \array_values($sets);

                continue;
            }

            ++$iterations;
        }
    }
}
