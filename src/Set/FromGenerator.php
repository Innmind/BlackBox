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
     */
    private function __construct(
        private \Closure $generatorFactory,
        private bool $immutable,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator {
        $generator = ($this->generatorFactory)($random);
        $iterations = 0;

        while ($iterations < $size && $generator->valid()) {
            /** @var T|Seed<T> */
            $value = $generator->current();
            $value = Value::of($value)
                ->mutable(!$this->immutable)
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
