<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<float>
 */
final class RealNumbers implements Set
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
        $this->predicate = fn(float $value): bool => $value >= $this->lowerBound && $value <= $this->upperBound;
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
            $value = \random_int($this->lowerBound, $this->upperBound) * \lcg_value();

            if (!($this->predicate)($value)) {
                continue;
            }

            yield Value::immutable($value, $this->shrink($value));
            ++$iterations;
        } while ($iterations < $this->size);
    }

    private function shrink(float $value): ?Dichotomy
    {
        if (\round($value, 5) === 0.0) {
            return null;
        }

        return new Dichotomy(
            $this->divideByTwo($value),
            $this->reduceByOne($value),
        );
    }

    private function divideByTwo(float $value): callable
    {
        $shrinked = $value / 2;

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    private function reduceByOne(float $value): callable
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
    private function identity(float $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }
}
