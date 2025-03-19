<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Seed;

use Innmind\BlackBox\Set\{
    Value,
    Dichotomy,
    Seed,
};

/**
 * @internal
 * @template T
 */
final class Map
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed): T $map
     */
    private function __construct(
        private Value $value,
        private \Closure $map,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     * @template A
     *
     * @param Value<A> $value
     *
     * @return self<A>
     */
    public static function of(Value $value): self
    {
        return new self($value, static fn($value): mixed => $value);
    }

    /**
     * @psalm-mutation-free
     * @template U
     *
     * @param callable(T): U $map
     *
     * @return self<U>
     */
    public function map(callable $map): self
    {
        $previous = $this->map;

        return new self(
            $this->value,
            static fn($value) => $map($previous($value)),
        );
    }

    /**
     * @psalm-mutation-free
     * @template U
     *
     * @param callable(T): Seed<U> $map
     *
     * @return FlatMap<U>
     */
    public function flatMap(callable $map): FlatMap
    {
        /** @psalm-suppress InvalidArgument */
        return FlatMap::of($this, $map);
    }

    /**
     * @param \Closure(T): bool $predicate
     *
     * @return ?Dichotomy<T>
     */
    public function shrink(\Closure $predicate): ?Dichotomy
    {
        $shrunk = $this->value->shrink();

        if (\is_null($shrunk)) {
            return null;
        }

        // There's no need to define the immutability of the values here because
        // it's held by the values injected in the new Seeds.
        // No dichotomy because the captured values in the configure lambda is
        // shrunk first
        $a = Value::of(Seed::of($shrunk->a())->map($this->map))
            ->predicatedOn($predicate);
        $b = Value::of(Seed::of($shrunk->b())->map($this->map))
            ->predicatedOn($predicate);

        // If one of the strategies is not acceptable then we remove it and it
        // will de defaulted to the parent value. And if both of them are not
        // acceptable then the shrinking stops.
        if (!$a->acceptable()) {
            $a = null;
        }

        if (!$b->acceptable()) {
            $b = null;
        }

        return Dichotomy::of($a, $b);
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return ($this->map)($this->value->unwrap());
    }
}
