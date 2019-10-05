<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class FromGenerator implements Set
{
    private $size;
    private $generatorFactory;
    private $predicate;
    private $values;

    /**
     * @param callable(): \Generator $generatorFactory
     */
    public function __construct(callable $generatorFactory)
    {
        if (!$generatorFactory() instanceof \Generator) {
            throw new \TypeError('Argument 1 must be of type callable(): \Generator');
        }

        $this->size = 100;
        $this->generatorFactory = $generatorFactory;
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
        $self->values = null;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->predicate = function($value) use ($predicate): bool {
            if (!($this->predicate)($value)) {
                return false;
            }

            return $predicate($value);
        };
        $self->values = null;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        if (\is_null($this->values)) {
            $this->values = \iterator_to_array($this->values());
        }

        return \array_reduce($this->values, $reducer, $carry);
    }

    /**
     * {@inheritdoc}
     */
    public function values(): \Generator
    {
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
