<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Value;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Seed,
    Value,
};

/**
 * @internal
 * @template-covariant T
 */
final class Immutable
{
    private ?Seed $seed = null;

    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed): (T|Seed<T>) $unwrap
     * @param T|Seed<T> $unwrapped
     */
    private function __construct(
        private mixed $source,
        private \Closure $unwrap,
        private mixed $unwrapped,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template V
     *
     * @param V|Seed<V> $value
     *
     * @return self<V>
     */
    public static function of($value): self
    {
        /** @var self<V> */
        return new self(
            $value,
            static fn($source): mixed => $source,
            $value,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>|Mutable<T>
     */
    public function mutable(bool $mutable): self|Mutable
    {
        if (!$mutable) {
            return $this;
        }

        return new Mutable(
            $this->source,
            $this->unwrap,
        );
    }

    /**
     * @psalm-mutation-free
     * @template V
     *
     * @param callable(T): (V|Seed<V>) $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        $previous = $this->unwrap;
        $unwrap = static function(mixed $source) use ($map, $previous): mixed {
            $value = $previous($source);

            if ($value instanceof Seed) {
                return $value->flatMap(static function($value) use ($map) {
                    /** @var T $value */
                    $mapped = $map($value);

                    if ($mapped instanceof Seed) {
                        return $mapped;
                    }

                    return Seed::of(Value::of($mapped));
                });
            }

            return $map($value);
        };

        // avoid recomputing the map operation on each unwrap
        /** @psalm-suppress ImpureFunctionCall Since everything is supposed immutable this should be fine */
        $value = $unwrap($this->source);
        $unwrap = static fn(): mixed => $value;

        /** @var self<V> */
        return new self(
            $this->source,
            $unwrap,
            $value,
        );
    }

    /**
     * @param \Closure(self<T>): Value<T> $wrap
     * @param \Closure(Value<T>, Value<T>): ?Dichotomy<T> $shrink
     * @param \Closure(mixed): bool $predicate
     *
     * @return ?Dichotomy<T>
     */
    public function shrink(
        \Closure $wrap,
        \Closure $shrink,
        \Closure $predicate,
    ): ?Dichotomy {
        $identity = $wrap($this)->withoutShrinking();

        return $shrink($wrap($this), $identity) ?? $this->seed?->shrink($predicate)?->default($identity);
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        $value = $this->unwrapped;

        // This is not ideal to hide the seeded value this way and to hijack
        // the shrinking system in self::shrinkable() and self::shrink() as it
        // complexifies the understanding of what's happening. Because now the
        // filtering can happen in 2 places.
        // Until a better idea comes along, this will stay this way.
        if ($value instanceof Seed) {
            $this->seed = $value;
            /** @var T */
            $value = $value->unwrap();
        }

        return $value;
    }
}
