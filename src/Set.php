<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Set\Implementation,
    Set\Provider,
    Set\Value,
    Set\Collapse,
    Exception\EmptySet,
};

/**
 * @template-covariant T The type of data being generated
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
    public static function of(mixed $first, mixed ...$rest): self
    {
        return new self(Set\Elements::implementation($first, ...$rest));
    }

    /**
     * @psalm-pure
     */
    public static function integers(): Provider\Integers
    {
        /** @psalm-suppress InvalidArgument */
        return Provider\Integers::of(self::build(...));
    }

    /**
     * @psalm-pure
     */
    public static function realNumbers(): Provider\RealNumbers
    {
        /** @psalm-suppress InvalidArgument */
        return Provider\RealNumbers::of(self::build(...));
    }

    /**
     * By default the value created by this composition is considered immutable.
     *
     * @psalm-pure
     *
     * @template A
     * @no-named-arguments
     *
     * @param callable(mixed...): A $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return Provider\Composite<A>
     */
    public static function composite(
        callable $aggregate,
        self|Provider $first,
        self|Provider $second,
        self|Provider ...$rest,
    ): Provider\Composite {
        /**
         * @psalm-suppress InvalidArgument
         * @psalm-suppress ImpurePropertyFetch Only the ::values() method is impure
         * @psalm-suppress ImpureFunctionCall
         */
        return Provider\Composite::of(
            self::build(...),
            $aggregate,
            Collapse::of($first)->implementation,
            Collapse::of($second)->implementation,
            ...\array_map(
                static fn($set) => Collapse::of($set)->implementation,
                $rest,
            ),
        );
    }

    /**
     * By default the value created by this generator is considered immutable.
     *
     * This set can only contain immutable values as they're generated outside of the
     * class, so it can't be re-generated on the fly
     *
     * @psalm-pure
     *
     * @template V
     *
     * @param callable(Random): \Generator<V> $factory
     *
     * @return Provider\Generator<V>
     */
    public static function generator(callable $factory): Provider\Generator
    {
        /** @psalm-suppress InvalidArgument */
        return Provider\Generator::of(
            self::build(...),
            $factory,
        );
    }

    /**
     * @psalm-pure
     *
     * @template V
     *
     * @param self<V>|Provider<V> $set
     *
     * @return Provider\Sequence<V>
     */
    public static function sequence(self|Provider $set): Provider\Sequence
    {
        /**
         * @psalm-suppress InvalidArgument
         * @psalm-suppress ImpurePropertyFetch Only the ::values() method is impure
         */
        return Provider\Sequence::of(
            self::build(...),
            Collapse::of($set)->implementation,
        );
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
        /**
         * @psalm-suppress ImpurePropertyFetch Only the ::values() method is impure
         * @psalm-suppress ImpureFunctionCall
         */
        return new self(Set\Either::implementation(
            Collapse::of($first)->implementation,
            Collapse::of($second)->implementation,
            ...\array_map(
                static fn($set) => Collapse::of($set)->implementation,
                $rest,
            ),
        ));
    }

    /**
     * @psalm-pure
     *
     * @return self<string>
     */
    public static function unsafeStrings(): self
    {
        return new self(Set\UnsafeStrings::implementation());
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<?T>
     */
    public function nullable(): self
    {
        return self::either($this, self::of(null));
    }

    /**
     * Use this set to prevent iterating over all possible combinations of a composite set
     *
     * It will allow to test more diverse combinations for a given set
     *
     * @psalm-mutation-free
     *
     * @return self<T>
     */
    public function randomize(): self
    {
        return new self(Set\Randomize::implementation($this->implementation));
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

    /**
     * @template A
     * @psalm-pure
     *
     * @param Implementation<A> $implementation
     *
     * @return self<A>
     */
    private static function build(Implementation $implementation): self
    {
        return new self($implementation);
    }
}
