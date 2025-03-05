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
    public static function compose(
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
     * @psalm-pure
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
     * @return self<non-empty-list<A|B|C>>
     */
    public static function tuple(
        self|Provider $first,
        self|Provider $second,
        self|Provider ...$rest,
    ): self {
        /** @var self<non-empty-list<A|B|C>> */
        return self::compose(
            static fn(mixed ...$args) => $args,
            $first,
            $second,
            ...$rest,
        )
            ->immutable()
            ->toSet();
    }

    /**
     * The value generated by this decorator is considered mutable.
     *
     * If you need the value to be immutable or derived from the underlying
     * value, use self::map() instead.
     *
     * @psalm-pure
     *
     * @template A
     * @template B
     *
     * @param callable(A): B $decorate
     * @param Set<A>|Provider<A> $set
     *
     * @return self<B>
     */
    public static function decorate(
        callable $decorate,
        self|Provider $set,
    ): self {
        /**
         * @psalm-suppress InvalidArgument
         * @psalm-suppress ImpurePropertyFetch Only the ::values() method is impure
         * @psalm-suppress ImpureFunctionCall
         */
        return new self(Set\Map::implementation(
            $decorate,
            Collapse::of($set)->implementation,
            false,
        ));
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
     * @template A
     *
     * @param callable(): A $call
     *
     * @return self<A>
     */
    public static function call(callable $call): self
    {
        return self::generator(static function() use ($call) {
            while (true) {
                yield $call;
            }
        })
            ->mutable()
            ->map(static fn($call) => $call());
    }

    /**
     * @psalm-pure
     */
    public static function strings(): Provider\Strings
    {
        /**
         * @psalm-suppress InvalidArgument
         * @psalm-suppress ImpurePropertyFetch Only the ::values() method is impure
         */
        return Provider\Strings::of(self::build(...));
    }

    /**
     * @psalm-pure
     *
     * @return self<non-empty-string>
     */
    public static function email(): self
    {
        $letter = static fn(string ...$extra): self => self::of(
            ...\range('a', 'z'),
            ...\range('A', 'Z'),
            ...\array_map(
                static fn($i) => (string) $i,
                \range(0, 9),
            ),
            ...$extra,
        );
        /** @psalm-suppress ArgumentTypeCoercion */
        $string = static fn(int $maxLength, string ...$extra): self => self::either(
            // either only with simple characters
            self::sequence($letter())
                ->between(1, $maxLength)
                ->map(static fn(array $chars): string => \implode('', $chars)),
            // or with some extra ones in the middle
            self::compose(
                static fn(string ...$parts): string => \implode('', $parts),
                $letter(),
                self::sequence($letter(...$extra))
                    ->between(1, $maxLength - 2)
                    ->map(static fn(array $chars): string => \implode('', $chars)),
                $letter(),
            )
                ->immutable()
                ->filter(static function(string $string): bool {
                    return !\preg_match('~\.\.~', $string);
                }),
        );
        $address = $string(64, '-', '.', '_');
        $domain = $string(63, '-', '.');
        $tld = self::sequence(self::of(...\range('a', 'z'), ...\range('A', 'Z')))
            ->between(1, 63)
            ->map(static fn(array $chars): string => \implode('', $chars));

        return self::compose(
            static fn(string $address, string $domain, string $tld) => "$address@$domain.$tld",
            $address,
            $domain,
            $tld,
        )
            ->immutable()
            ->take(100)
            ->filter(static function(string $email): bool {
                return !\preg_match('~(\-.|\.\-)~', $email);
            });
    }

    /**
     * Use this set to prove your code is indifferent to the value passed to it
     *
     * @return self<mixed>
     */
    public static function type(): self
    {
        // no resource is generated as it may result in a fatal error of too
        // many opened resources
        /** @psalm-suppress InvalidArgument Don't why it complains */
        $primitives = self::either(
            self::of(true, false, null),
            self::integers(),
            self::realNumbers(),
            self::strings()->unicode(),
            self::generator(static function() { // objects
                while (true) {
                    yield new class {
                    };
                }
            }),
            self::generator(static function() { // callables
                while (true) {
                    yield new class {
                        public function __invoke()
                        {
                        }
                    };
                    yield static fn() => null;
                    yield static fn() => null;
                }
            }),
        );

        return self::either(
            $primitives,
            self::sequence($primitives)->between(0, 1), // no more needed to prove type indifference
            self::sequence($primitives)
                ->between(0, 1) // no more needed to prove type indifference
                ->map(static fn(array $array): \Iterator => new \ArrayIterator($array)),
        );
    }

    /**
     * @psalm-pure
     *
     * @return self<non-empty-string>
     */
    public static function uuid(): self
    {
        $chars = self::of(...\range('a', 'f'), ...\range(0, 9));
        /** @psalm-suppress ArgumentTypeCoercion */
        $part = static fn(int $length): self => self::sequence($chars)
            ->between($length, $length)
            ->map(static fn(array $chars): string => \implode('', $chars));

        /** @var self<non-empty-string> */
        return self::compose(
            static fn(string ...$parts): string => \implode('-', $parts),
            $part(8),
            $part(4),
            $part(4),
            $part(4),
            $part(12),
        )
            ->immutable()
            ->take(100);
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<?T>
     */
    public function nullable(): self
    {
        return new self(Set\Either::implementation(
            $this->implementation,
            Set\Elements::implementation(null),
        ));
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
     * This allows to configure a Set from a randomly generated value from the
     * current Set.
     *
     * Note that the value generated for the input won't be shrunk. The more
     * your values comes from this composition the less values will be
     * shrinkable.
     *
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(T): (self<V>|Provider<V>) $map
     *
     * @return self<V>
     */
    public function flatMap(callable $map): self
    {
        return new self($this->implementation->flatMap(
            $map,
            static fn(self|Provider $set) => Collapse::of($set)->implementation,
        ));
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
