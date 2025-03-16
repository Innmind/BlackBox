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
final class Mutable
{
    private ?Seed $seed = null;

    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed): (T|Seed<T>) $unwrap
     * @param \Closure(Value<T>, Value<T>): ?Dichotomy<T> $shrink
     * @param \Closure(mixed): bool $predicate
     */
    public function __construct(
        private mixed $source,
        private \Closure $unwrap,
        private \Closure $shrink,
        private \Closure $predicate,
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
        return new self(
            $value,
            static fn($source): mixed => $source,
            static fn() => null,
            static fn() => true,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function mutable(bool $mutable): self
    {
        // Mutable values can't become immutable
        return $this;
    }

    /**
     * @psalm-mutation-free
     *
     * @param \Closure(Value<T>): ?Dichotomy<T> $shrink
     *
     * @return self<T>
     */
    public function shrinkWith(\Closure $shrink): self
    {
        return new self(
            $this->source,
            $this->unwrap,
            static fn(Value $self, Value $default) => $shrink($self)?->default($default),
            $this->predicate,
        );
    }

    /**
     * @return self<T>
     */
    public function withoutShrinking(): self
    {
        return new self(
            $this->source,
            $this->unwrap,
            static fn() => null,
            $this->predicate,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(mixed): bool $predicate
     *
     * @return self<T>
     */
    public function predicatedOn(callable $predicate): self
    {
        return new self(
            $this->source,
            $this->unwrap,
            $this->shrink,
            \Closure::fromCallable($predicate),
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

        return new self(
            $this->source,
            $unwrap,
            static fn() => null,
            $this->predicate,
        );
    }

    /**
     * @param \Closure(self<T>): Value<T> $wrap
     *
     * @return ?Dichotomy<T>
     */
    public function shrink(\Closure $wrap): ?Dichotomy
    {
        $identity = $wrap($this->withoutShrinking());

        return ($this->shrink)($wrap($this), $identity) ?? $this->seed?->shrink($this->predicate)?->default($identity);
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        $value = ($this->unwrap)($this->source);

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
