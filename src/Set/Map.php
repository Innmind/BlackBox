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
            $mutable = !($value->isImmutable() && $this->immutable);

            yield Value::immutable($value)
                ->flagMutable($mutable)
                ->map(static fn($value) => $value->unwrap())
                ->map($this->map)
                ->predicatedOn($predicate)
                ->shrinkWith(fn() => $this->shrink($mutable, $value, $predicate));
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
        return Value::immutable($strategy->unwrap())
            ->flagMutable($mutable)
            ->map($this->map)
            ->predicatedOn($predicate)
            ->shrinkWith(fn() => $this->shrink($mutable, $strategy, $predicate));
    }
}
