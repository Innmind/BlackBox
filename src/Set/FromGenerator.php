<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * @internal
 * @template T
 * @implements Implementation<T>
 */
final class FromGenerator implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(Random): \Generator<T|Seed<T>> $generatorFactory
     * @param int<1, max> $size
     */
    private function __construct(
        private \Closure $generatorFactory,
        private int $size,
        private bool $immutable,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template V
     *
     * @param callable(Random): \Generator<V|Seed<V>> $generatorFactory
     *
     * @return self<V>
     */
    public static function implementation(
        callable $generatorFactory,
        bool $immutable,
    ): self {
        return new self(
            \Closure::fromCallable($generatorFactory),
            100,
            $immutable,
        );
    }

    /**
     * @deprecated Use Set::generator()->immutable() instead
     * @template V
     *
     * @param callable(Random): \Generator<V> $generatorFactory
     *
     * @return Set<V>
     */
    public static function of(callable $generatorFactory): Set
    {
        return Set::generator(self::guard($generatorFactory))
            ->immutable()
            ->toSet();
    }

    /**
     * @deprecated Use Set::generator()->mutable() instead
     * @template V
     *
     * @param callable(Random): \Generator<V> $generatorFactory
     *
     * @return Set<V>
     */
    public static function mutable(callable $generatorFactory): Set
    {
        return Set::generator(self::guard($generatorFactory))
            ->mutable()
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->generatorFactory,
            $size,
            $this->immutable,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $generator = ($this->generatorFactory)($random);
        $iterations = 0;

        while ($iterations < $this->size && $generator->valid()) {
            /** @var T|Seed<T> */
            $value = $generator->current();
            $value = Value::of($value)
                ->flagMutable(!$this->immutable)
                ->predicatedOn($predicate);

            if ($value->acceptable()) {
                yield $value;

                ++$iterations;
            }

            $generator->next();
        }

        if ($iterations === 0) {
            throw new EmptySet;
        }
    }

    /**
     * @template A
     *
     * @param callable(Random): \Generator<A> $generatorFactory
     *
     * @return callable(Random): \Generator<A>
     */
    private static function guard(callable $generatorFactory): callable
    {
        if (!$generatorFactory(Random::mersenneTwister) instanceof \Generator) {
            throw new \TypeError('Argument 1 must be of type callable(): \Generator');
        }

        return $generatorFactory;
    }
}
