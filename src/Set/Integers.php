<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<int>
 */
final class Integers implements Set
{
    private int $lowerBound;
    private int $upperBound;
    private int $size;
    private \Closure $predicate;

    private function __construct(int $lowerBound = null, int $upperBound = null)
    {
        $this->lowerBound = $lowerBound ?? \PHP_INT_MIN;
        $this->upperBound = $upperBound ?? \PHP_INT_MAX;
        $this->size = 100;
        $this->predicate = static fn(): bool => true;
    }

    public static function any(): self
    {
        return new self;
    }

    public static function between(int $lowerBound, int $upperBound): self
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
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(): \Generator
    {
        $iterations = 0;

        do {
            $value = \random_int($this->lowerBound, $this->upperBound);

            if (!($this->predicate)($value)) {
                continue;
            }

            yield Value::immutable($value, $this->shrink($value));
            ++$iterations;
        } while ($iterations < $this->size);
    }

    private function shrink(int $value): ?Dichotomy
    {
        if ($value === 0) {
            return null;
        }

        return new Dichotomy(
            $this->divideByTwo($value),
            $this->reduceByOne($value),
        );
    }

    private function divideByTwo(int $value): callable
    {
        $shrinked = (int) \round($value / 2, 0, \PHP_ROUND_HALF_DOWN);

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    private function reduceByOne(int $value): callable
    {
        // add one when the value is negative, otherwise subtract one
        $reduce = ($value <=> 0) * -1;
        $shrinked = $value + $reduce;

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * Non shrinkable as it is alreay the minimum value accepted by the predicate
     */
    private function identity(int $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }
}
