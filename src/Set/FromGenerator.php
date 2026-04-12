<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

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
    ): \Generator {
        $generator = ($this->generatorFactory)($random);

        foreach ($generator as $value) {
            $value = Value::of($value)
                ->mutable(!$this->immutable)
                ->predicatedOn($predicate);

            yield $value;
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
