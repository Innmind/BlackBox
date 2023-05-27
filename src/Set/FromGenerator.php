<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * This set can only contain immutable values as they're generated outside of the
 * class, so it can't be re-generated on the fly
 *
 * @template T
 * @implements Set<T>
 */
final class FromGenerator implements Set
{
    /** @var positive-int */
    private int $size;
    /** @var \Closure(Random): \Generator<T> */
    private \Closure $generatorFactory;
    /** @var \Closure(T): bool */
    private \Closure $predicate;

    /**
     * @param callable(Random): \Generator<T> $generatorFactory
     * @param positive-int $size
     * @param \Closure(T): bool $predicate
     */
    private function __construct(
        callable $generatorFactory,
        int $size,
        \Closure $predicate,
    ) {
        $this->generatorFactory = \Closure::fromCallable($generatorFactory);
        $this->size = $size;
        $this->predicate = $predicate;
    }

    /**
     * @template V
     *
     * @param callable(Random): \Generator<V> $generatorFactory
     *
     * @return self<V>
     */
    public static function of(callable $generatorFactory): self
    {
        if (!$generatorFactory(Random::mersenneTwister) instanceof \Generator) {
            throw new \TypeError('Argument 1 must be of type callable(): \Generator');
        }

        return new self($generatorFactory, 100, static fn(): bool => true);
    }

    public function take(int $size): Set
    {
        return new self(
            $this->generatorFactory,
            $size,
            $this->predicate,
        );
    }

    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;

        return new self(
            $this->generatorFactory,
            $this->size,
            static function(mixed $value) use ($previous, $predicate): bool {
                /** @var T */
                $value = $value;

                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
        );
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        $generator = ($this->generatorFactory)($random);
        $iterations = 0;

        while ($iterations < $this->size && $generator->valid()) {
            $value = $generator->current();

            if (($this->predicate)($value)) {
                yield Value::immutable($value);

                ++$iterations;
            }

            $generator->next();
        }

        if ($iterations === 0) {
            throw new EmptySet;
        }
    }
}
