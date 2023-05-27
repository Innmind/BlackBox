<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @implements Set<int>
 */
final class Integers implements Set
{
    private int $lowerBound;
    private int $upperBound;
    private int $size;
    private \Closure $predicate;

    private function __construct(int $lowerBound, int $upperBound)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->size = 100;
        $this->predicate = fn(int $value): bool => $value >= $this->lowerBound && $value <= $this->upperBound;
    }

    public static function any(): self
    {
        return new self(\PHP_INT_MIN, \PHP_INT_MAX);
    }

    /**
     * @psalm-mutation-free
     */
    public static function between(int $lowerBound, int $upperBound): self
    {
        return new self($lowerBound, $upperBound);
    }

    public static function above(int $lowerBound): self
    {
        return new self($lowerBound, \PHP_INT_MAX);
    }

    public static function below(int $upperBound): self
    {
        return new self(\PHP_INT_MIN, $upperBound);
    }

    public function lowerBound(): int
    {
        return $this->lowerBound;
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
        $self->predicate = static function(int $value) use ($previous, $predicate): bool {
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(Random $random): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            $value = $random->between($this->lowerBound, $this->upperBound);

            if (!($this->predicate)($value)) {
                continue;
            }

            yield Value::immutable($value, $this->shrink($value));
            ++$iterations;
        }
    }

    /**
     * @return Dichotomy<int>|null
     */
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

    /**
     * @return callable(): Value<int>
     */
    private function divideByTwo(int $value): callable
    {
        $shrinked = (int) \round($value / 2, 0, \PHP_ROUND_HALF_DOWN);

        if (!($this->predicate)($shrinked)) {
            return $this->reduceByOne($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * @return callable(): Value<int>
     */
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
     *
     * @return callable(): Value<int>
     */
    private function identity(int $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }
}
