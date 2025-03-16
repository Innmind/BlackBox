<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

/**
 * @internal
 * @template-covariant T
 */
final class Value
{
    private ?Seed $seed = null;

    /**
     * @psalm-mutation-free
     *
     * @param Value\Immutable<T>|Value\Mutable<T> $implementation
     * @param \Closure(Value<T>, Value<T>): ?Dichotomy<T> $shrink
     * @param \Closure(mixed): bool $predicate
     */
    private function __construct(
        private Value\Immutable|Value\Mutable $implementation,
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
            Value\Immutable::of($value),
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
        return new self(
            $this->implementation->mutable($mutable),
            $this->shrink,
            $this->predicate,
        );
    }

    /**
     * @param \Closure(self<T>): ?Dichotomy<T> $shrink
     *
     * @return self<T>
     */
    public function shrinkWith(\Closure $shrink): self
    {
        return new self(
            $this->implementation,
            static fn(self $self, self $default) => $shrink($self)?->default($default),
            $this->predicate,
        );
    }

    /**
     * @return self<T>
     */
    public function withoutShrinking(): self
    {
        return new self(
            $this->implementation,
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
            $this->implementation,
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
        return new self(
            $this->implementation->map($map),
            static fn() => null,
            $this->predicate,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): T $shrink
     *
     * @return self<T>
     */
    public function shrinkVia(callable $shrink): self
    {
        return new self(
            $this->implementation->shrinkVia($shrink),
            $this->shrink,
            $this->predicate,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(mixed): mixed $shrink
     *
     * @return ?self<T>
     */
    public function maybeShrinkVia(callable $shrink): ?self
    {
        $shrunk = $this->implementation->maybeShrinkVia($shrink);

        if (\is_null($shrunk)) {
            return null;
        }

        return new self(
            $shrunk,
            $this->shrink,
            $this->predicate,
        );
    }

    public function acceptable(): bool
    {
        return ($this->predicate)($this->unwrap());
    }

    /**
     * @psalm-mutation-free
     */
    public function immutable(): bool
    {
        return $this->implementation instanceof Value\Immutable;
    }

    /**
     * @return ?Dichotomy<T>
     */
    public function shrink(): ?Dichotomy
    {
        $identity = $this->withoutShrinking();

        return ($this->shrink)($this, $identity) ?? $this
            ->seed
            ?->shrink($this->predicate)
            ?->default($identity);
    }

    /**
     * @return T
     */
    public function unwrap()
    {
        $value = $this->implementation->unwrap();

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
