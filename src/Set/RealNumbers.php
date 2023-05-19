<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @implements Set<float>
 */
final class RealNumbers implements Set
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
        $this->predicate = fn(float $value): bool => $value >= $this->lowerBound && $value <= $this->upperBound;
    }

    public static function any(): self
    {
        return new self(\PHP_INT_MIN, \PHP_INT_MAX);
    }

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
        $self->predicate = static function(float $value) use ($previous, $predicate): bool {
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
    public function values(Random $rand): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            // simulate the function lcg_value()
            $lcg = ($rand(0, 100) / 100);
            /** @var float */
            $value = $rand($this->lowerBound, $this->upperBound) * $lcg;

            if (!($this->predicate)($value)) {
                continue;
            }

            yield Value::immutable($value, $this->shrink($value));
            ++$iterations;
        }
    }

    /**
     * @return Dichotomy<float>|null
     */
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

    /**
     * @return callable(): Value<float>
     */
    private function divideByTwo(float $value): callable
    {
        $shrinked = $value / 2;

        if (!($this->predicate)($shrinked)) {
            return $this->reduceByOne($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * @return callable(): Value<float>
     */
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
     *
     * @return callable(): Value<float>
     */
    private function identity(float $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }
}
