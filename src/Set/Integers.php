<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class Integers implements Set
{
    private int $lowerBound;
    private int $upperBound;
    private int $size;
    private \Closure $predicate;

    public function __construct(int $lowerBound = null, int $upperBound = null)
    {
        $this->lowerBound = $lowerBound ?? \PHP_INT_MIN;
        $this->upperBound = $upperBound ?? \PHP_INT_MAX;
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function between(int $lowerBound = null, int $upperBound = null): self
    {
        return new self($lowerBound, $upperBound);
    }

    public static function above(int $lowerBound): self
    {
        return new self($lowerBound);
    }

    public static function below(int $upperBound): self
    {
        return new self(null, $upperBound);
    }

    /**
     * @deprecated
     * @see self::between()
     */
    public static function of(int $lowerBound = null, int $upperBound = null): self
    {
        return self::between($lowerBound, $upperBound);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;

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

        return $self;
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
