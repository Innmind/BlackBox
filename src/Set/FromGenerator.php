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
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
    ): \Generator {
        $generator = ($this->generatorFactory)($random);

        foreach ($generator as $value) {
            $value = Value::of($value)->predicatedOn($predicate);

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
    public static function implementation(callable $generatorFactory): self
    {
        return new self(
            \Closure::fromCallable($generatorFactory),
        );
    }
}
