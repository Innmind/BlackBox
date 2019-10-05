<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class Integers implements Set
{
    private $lowerBound;
    private $upperBound;
    private $size;
    private $predicate;
    private $values;

    public function __construct(int $lowerBound = null, int $upperBound = null)
    {
        $this->lowerBound = $lowerBound ?? \PHP_INT_MIN;
        $this->upperBound = $upperBound ?? \PHP_INT_MAX;
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(int $lowerBound = null, int $upperBound = null): self
    {
        return new self($lowerBound, $upperBound);
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
     * @return \Generator<int>
     */
    public function values(): \Generator
    {
        $iterations = 0;

        do {
            $value = \random_int($this->lowerBound, $this->upperBound);

            if (!($this->predicate)($value)) {
                continue;
            }

            yield $value;
            ++$iterations;
        } while ($iterations < $this->size);
    }
}
