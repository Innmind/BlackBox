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
     * @param Value\Map<D> $map
     * @param Implementation<I> $set
     */
    private function __construct(
        private Value\Map $map,
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
        if ($set instanceof self) {
            /** @psalm-suppress ImpurePropertyFetch */
            return new self(
                $set->map->with($map),
                $set->set,
                $set->immutable && $immutable,
            );
        }

        return new self(
            Value\Map::noop()->with($map),
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
            $mutable = !($value->immutable() && $this->immutable);

            yield Value::of($value)
                ->mutable($mutable)
                ->map(static fn($value) => $value->unwrap())
                ->map($this->map)
                ->predicatedOn($predicate)
                ->shrinkWith(Map\Shrinker::instance);
        }
    }
}
