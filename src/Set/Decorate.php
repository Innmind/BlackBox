<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @template D
 * @template I
 * @implements Set<D>
 */
final class Decorate implements Set
{
    /** @var \Closure(I): D */
    private \Closure $decorate;
    /** @var Set<I> */
    private Set $set;
    private bool $immutable;

    /**
     * @param callable(I): D $decorate
     * @param Set<I> $set
     */
    private function __construct(bool $immutable, callable $decorate, Set $set)
    {
        $this->decorate = \Closure::fromCallable($decorate);
        $this->set = $set;
        $this->immutable = $immutable;
    }

    /**
     * @template T
     * @template V
     *
     * @param callable(V): T $decorate It must be a pure function (no randomness, no side effects)
     * @param Set<V> $set
     *
     * @return self<T,V>
     */
    public static function immutable(callable $decorate, Set $set): self
    {
        return new self(true, $decorate, $set);
    }

    /**
     * @template T
     * @template V
     *
     * @param callable(V): T $decorate It must be a pure function (no randomness, no side effects)
     * @param Set<V> $set
     *
     * @return self<T,V>
     */
    public static function mutable(callable $decorate, Set $set): self
    {
        return new self(false, $decorate, $set);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->set = $this->set->take($size);

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        /** @psalm-suppress MixedArgument */
        $self->set = $this->set->filter(fn(mixed $value): bool => $predicate(($this->decorate)($value)));

        return $self;
    }

    public function map(callable $map): self
    {
        return self::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        foreach ($this->set->values($random) as $value) {
            if ($value->isImmutable() && $this->immutable) {
                $decorated = ($this->decorate)($value->unwrap());

                yield Value::immutable(
                    $decorated,
                    $this->shrink(false, $value),
                );
            } else {
                // we don't need to re-apply the predicate when we handle mutable
                // data as the underlying data is already validated and the mutable
                // nature is about the enclosing of the data and should not be part
                // of the filtering process
                yield Value::mutable(
                    fn() => ($this->decorate)($value->unwrap()),
                    $this->shrink(true, $value),
                );
            }
        }
    }

    /**
     * @param Value<I> $value
     *
     * @return Dichotomy<D>
     */
    private function shrink(bool $mutable, Value $value): ?Dichotomy
    {
        if (!$value->shrinkable()) {
            return null;
        }

        $shrinked = $value->shrink();

        return new Dichotomy(
            $this->shrinkWithStrategy($mutable, $shrinked->a()),
            $this->shrinkWithStrategy($mutable, $shrinked->b()),
        );
    }

    /**
     * @param Value<I> $strategy
     *
     * @return callable(): Value<D>
     */
    private function shrinkWithStrategy(bool $mutable, Value $strategy): callable
    {
        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => ($this->decorate)($strategy->unwrap()),
                $this->shrink(true, $strategy),
            );
        }

        return fn(): Value => Value::immutable(
            ($this->decorate)($strategy->unwrap()),
            $this->shrink(false, $strategy),
        );
    }
}
