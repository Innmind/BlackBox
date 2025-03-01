<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Set\Implementation,
    Set\Provider,
    Set\Value,
    Exception\EmptySet,
};

/**
 * @template T The type of data being generated
 */
final class Set
{
    /**
     * @psalm-mutation-free
     *
     * @param Implementation<T> $implementation
     */
    private function __construct(
        private Implementation $implementation,
    ) {
    }

    /**
     * @internal
     * @template A
     * @psalm-pure
     * @todo Remove once all previous sets are flagged as internal
     *
     * @param Implementation<A> $implementation
     *
     * @return self<A>
     */
    public static function of(Implementation $implementation): self
    {
        return new self($implementation);
    }

    /**
     * @todo rename to ::of() when current self::of() will no longer be needed
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @template A
     * @template B
     *
     * @param A $first
     * @param B $rest
     *
     * @return self<A|B>
     */
    public static function elements(mixed $first, mixed ...$rest): self
    {
        return new self(Set\Elements::implementation($first, ...$rest));
    }

    /**
     * @psalm-pure
     *
     * @template A
     *
     * @param self<A>|Provider<A> $set
     *
     * @return self<A>
     */
    public static function randomize(self|Provider $set): self
    {
        return new self(Set\Randomize::implementation($set));
    }

    /**
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @template A
     * @template B
     * @template C
     *
     * @param self<A>|Provider<A> $first
     * @param self<B>|Provider<B> $second
     * @param self<C>|Provider<C> $rest
     *
     * @return self<A|B|C>
     */
    public static function either(
        self|Provider $first,
        self|Provider $second,
        self|Provider ...$rest,
    ): self {
        return new self(Set\Either::implementation($first, $second, ...$rest));
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<?T>
     */
    public function nullable(): self
    {
        return self::either($this, self::elements(null));
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     *
     * @return self<T>
     */
    public function take(int $size): self
    {
        return new self($this->implementation->take($size));
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(T): bool $predicate
     *
     * @return self<T>
     */
    public function filter(callable $predicate): self
    {
        return new self($this->implementation->filter($predicate));
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(T): V $map
     *
     * @return self<V>
     */
    public function map(callable $map): self
    {
        return new self($this->implementation->map($map));
    }

    /**
     * @internal End users mustn't use this method directly (BC breaks may be introduced)
     *
     * @throws EmptySet When no value can be generated
     *
     * @return \Generator<Value<T>>
     */
    public function values(Random $random): \Generator
    {
        yield from $this->implementation->values($random);
    }
}
