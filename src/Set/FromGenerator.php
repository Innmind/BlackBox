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
    private bool $immutable;

    /**
     * @psalm-mutation-free
     *
     * @param callable(Random): \Generator<T> $generatorFactory
     * @param positive-int $size
     * @param \Closure(T): bool $predicate
     */
    private function __construct(
        callable $generatorFactory,
        int $size,
        \Closure $predicate,
        bool $immutable,
    ) {
        $this->generatorFactory = \Closure::fromCallable($generatorFactory);
        $this->size = $size;
        $this->predicate = $predicate;
        $this->immutable = $immutable;
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
        return new self(
            self::guard($generatorFactory),
            100,
            static fn(): bool => true,
            true,
        );
    }

    /**
     * @template V
     *
     * @param callable(Random): \Generator<V> $generatorFactory
     *
     * @return self<V>
     */
    public static function mutable(callable $generatorFactory): self
    {
        return new self(
            self::guard($generatorFactory),
            100,
            static fn(): bool => true,
            false,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function take(int $size): Set
    {
        return new self(
            $this->generatorFactory,
            $size,
            $this->predicate,
            $this->immutable,
        );
    }

    /**
     * @psalm-mutation-free
     */
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
            $this->immutable,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function map(callable $map): Set
    {
        return match ($this->immutable) {
            true => Decorate::immutable($map, $this),
            false => Decorate::mutable($map, $this),
        };
    }

    public function values(Random $random): \Generator
    {
        $generator = ($this->generatorFactory)($random);
        $iterations = 0;

        while ($iterations < $this->size && $generator->valid()) {
            /** @var T */
            $value = $generator->current();

            if (($this->predicate)($value)) {
                yield match ($this->immutable) {
                    true => Value::immutable($value),
                    false => Value::mutable(static fn() => $value),
                };

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
