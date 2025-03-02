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
    /** @var Implementation<T> */
    private Implementation $first;
    /** @var Implementation<U> */
    private Implementation $second;
    /** @var list<Implementation<V>> */
    private array $rest;
    /** @var positive-int */
    private int $size;

    /**
     * @psalm-mutation-free
     *
     * @no-named-arguments
     *
     * @param positive-int $size
     * @param Implementation<T> $first
     * @param Implementation<U> $second
     * @param Implementation<V> $rest
     */
    private function __construct(
        int $size,
        Implementation $first,
        Implementation $second,
        Implementation ...$rest,
    ) {
        $this->first = $first;
        $this->second = $second;
        $this->rest = $rest;
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
     * @param Implementation<A> $first
     * @param Implementation<B> $second
     * @param Implementation<C> $rest
     *
     * @return self<A, B, C>
     */
    public static function implementation(
        Implementation $first,
        Implementation $second,
        Implementation ...$rest,
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
                static fn(Implementation $set): Implementation => $set->take($size),
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
                static fn(Implementation $set): Implementation => $set->filter($predicate),
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
        /** @var list<Implementation<T>|Implementation<U>|Implementation<V>> */
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
