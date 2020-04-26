<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * This set can only contain immutable values as they're generated outside of the
 * class, so it can't be re-generated on the fly
 *
 * @template T
 */
final class FromGenerator implements Set
{
    private int $size;
    /** @var \Closure(Random): \Generator<T> */
    private \Closure $generatorFactory;
    /** @var \Closure(T): bool */
    private \Closure $predicate;

    /**
     * @param callable(Random): \Generator<T> $generatorFactory
     */
    public function __construct(callable $generatorFactory)
    {
        if (!$generatorFactory(new Random\MtRand) instanceof \Generator) {
            throw new \TypeError('Argument 1 must be of type callable(): \Generator');
        }

        $this->size = 100;
        $this->generatorFactory = \Closure::fromCallable($generatorFactory);
        $this->predicate = static fn(): bool => true;
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
        return new self($generatorFactory);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;
        $self = clone $this;
        /** @psalm-suppress MissingClosureParamType */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            /** @var T */
            $value = $value;

            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    public function values(Random $rand): \Generator
    {
        /** @var \Generator<T> */
        $generator = ($this->generatorFactory)($rand);
        $iterations = 0;

        while ($iterations < $this->size && $generator->valid()) {
            $value = $generator->current();

            if (($this->predicate)($value)) {
                yield Value::immutable($value);

                ++$iterations;
            }

            $generator->next();
        }
    }
}
