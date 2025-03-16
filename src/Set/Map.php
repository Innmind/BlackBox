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
            $mutable = !($value->immutable() && $this->immutable);

            /** @psalm-suppress InvalidArgument Due to shrinker */
            yield Value::of($value)
                ->mutable($mutable)
                ->map(static fn($value) => $value->unwrap())
                ->map($this->map)
                ->predicatedOn($predicate)
                ->shrinkWith(self::shrink(...));
        }
    }

    /**
     * @template T
     *
     * @param Value<T> $value
     *
     * @return ?Dichotomy<T>
     */
    private static function shrink(Value $value): ?Dichotomy
    {
        $a = $value->maybeShrinkVia(static fn(Value $source) => $source->shrink()?->a());
        $b = $value->maybeShrinkVia(static fn(Value $source) => $source->shrink()?->b());

        if (!$a?->acceptable()) {
            $a = null;
        }

        if (!$b?->acceptable()) {
            $b = null;
        }

        return Dichotomy::of($a, $b);
    }
}
