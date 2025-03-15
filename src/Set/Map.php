<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @template D
 * @template I
 * @implements Implementation<D>
 */
final class Map implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(I): (Seed<D>|D) $map
     * @param Implementation<I> $set
     */
    private function __construct(
        private \Closure $map,
        private Implementation $set,
        private bool $immutable,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template T
     * @template V
     *
     * @param callable(V): (Seed<T>|T) $map It must be a pure function (no randomness, no side effects)
     * @param Implementation<V> $set
     *
     * @return self<T,V>
     */
    public static function implementation(
        callable $map,
        Implementation $set,
        bool $immutable,
    ): self {
        return new self(
            \Closure::fromCallable($map),
            $set,
            $immutable,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->map,
            $this->set->take($size),
            $this->immutable,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $map = $this->map;
        $mappedPredicate = static function(mixed $value) use ($map, $predicate): bool {
            /** @var I $value */
            $mapped = $map($value);

            if ($mapped instanceof Seed) {
                /** @var D */
                $mapped = $mapped->unwrap();
            }

            return $predicate($mapped);
        };

        foreach ($this->set->values($random, $mappedPredicate) as $value) {
            if ($value->isImmutable() && $this->immutable) {
                $mapped = ($this->map)($value->unwrap());

                yield Value::immutable($mapped)
                    ->predicatedOn($predicate)
                    ->shrinkWith(fn() => $this->shrink(false, $value, $predicate));
            } else {
                // we don't need to re-apply the predicate when we handle mutable
                // data as the underlying data is already validated and the mutable
                // nature is about the enclosing of the data and should not be part
                // of the filtering process
                yield Value::mutable(fn() => ($this->map)($value->unwrap()))
                    ->predicatedOn($predicate)
                    ->shrinkWith(fn() => $this->shrink(true, $value, $predicate));
            }
        }
    }

    /**
     * @param Value<I> $value
     * @param \Closure(D): bool $predicate
     *
     * @return ?Dichotomy<D>
     */
    private function shrink(
        bool $mutable,
        Value $value,
        \Closure $predicate,
    ): ?Dichotomy {
        $shrunk = $value->shrink();

        if (\is_null($shrunk)) {
            return null;
        }

        return Dichotomy::of(
            $this->shrinkWithStrategy($mutable, $shrunk->a(), $predicate),
            $this->shrinkWithStrategy($mutable, $shrunk->b(), $predicate),
        );
    }

    /**
     * @param Value<I> $strategy
     * @param \Closure(D): bool $predicate
     *
     * @return Value<D>
     */
    private function shrinkWithStrategy(
        bool $mutable,
        Value $strategy,
        \Closure $predicate,
    ): Value {
        if ($mutable) {
            return Value::mutable(fn() => ($this->map)($strategy->unwrap()))
                ->predicatedOn($predicate)
                ->shrinkWith(fn() => $this->shrink(true, $strategy, $predicate));
        }

        return Value::immutable(($this->map)($strategy->unwrap()))
            ->predicatedOn($predicate)
            ->shrinkWith(fn() => $this->shrink(false, $strategy, $predicate));
    }
}
