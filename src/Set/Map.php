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
     * @param \Closure(I): D $map
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
     * @param callable(V): T $map It must be a pure function (no randomness, no side effects)
     * @param Implementation<V> $set
     *
     * @return self<T,V>
     */
    public static function implementation(
        callable $map,
        Implementation $set,
        bool $immutable,
    ): self {
        return new self(\Closure::fromCallable($map), $set, $immutable);
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

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): self
    {
        /** @psalm-suppress MixedArgument */
        return new self(
            $this->map,
            $this->set->filter(fn(mixed $value): bool => $predicate(($this->map)($value))),
            $this->immutable,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function flatMap(callable $map, callable $extract): Implementation
    {
        /** @psalm-suppress MixedArgument Due to $input */
        return FlatMap::implementation(
            static fn($input) => $extract($map($input)),
            $this,
        );
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        foreach ($this->set->values($random) as $value) {
            if ($value->isImmutable() && $this->immutable) {
                $mapped = ($this->map)($value->unwrap());

                yield Value::immutable($mapped)
                    ->shrinkWith($this->shrink(false, $value));
            } else {
                // we don't need to re-apply the predicate when we handle mutable
                // data as the underlying data is already validated and the mutable
                // nature is about the enclosing of the data and should not be part
                // of the filtering process
                yield Value::mutable(fn() => ($this->map)($value->unwrap()))
                    ->shrinkWith($this->shrink(true, $value));
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

        return Dichotomy::of(
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
            return fn(): Value => Value::mutable(fn() => ($this->map)($strategy->unwrap()))
                ->shrinkWith($this->shrink(true, $strategy));
        }

        return fn(): Value => Value::immutable(($this->map)($strategy->unwrap()))
            ->shrinkWith($this->shrink(false, $strategy));
    }
}
