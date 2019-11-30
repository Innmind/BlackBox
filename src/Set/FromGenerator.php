<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @template T
 */
final class FromGenerator implements Set
{
    private int $size;
    private \Closure $generatorFactory;
    private \Closure $predicate;

    /**
     * @param callable(): \Generator<T> $generatorFactory
     */
    public function __construct(callable $generatorFactory)
    {
        if (!$generatorFactory() instanceof \Generator) {
            throw new \TypeError('Argument 1 must be of type callable(): \Generator');
        }

        $this->size = 100;
        $this->generatorFactory = \Closure::fromCallable($generatorFactory);
        $this->predicate = static function(): bool {
            return true;
        };
    }

    /**
     * @param callable(): \Generator<T> $generatorFactory
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
        $self = clone $this;
        /**
         * @psalm-suppress MissingClosureParamType
         */
        $self->predicate = function($value) use ($predicate): bool {
            if (!($this->predicate)($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    public function values(): \Generator
    {
        /** @var \Generator<T> */
        $generator = ($this->generatorFactory)();
        $iterations = 0;

        while ($iterations < $this->size && $generator->valid()) {
            $value = $generator->current();

            if (($this->predicate)($value)) {
                yield $value;

                ++$iterations;
            }

            $generator->next();
        }
    }
}
