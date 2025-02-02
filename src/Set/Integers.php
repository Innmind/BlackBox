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
    /** @var positive-int */
    private int $size;
    /** @var \Closure(int): bool */
    private \Closure $predicate;

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     * @param \Closure(int): bool $predicate
     */
    private function __construct(
        int $lowerBound,
        int $upperBound,
        ?int $size = null,
        ?\Closure $predicate = null,
    ) {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
        $this->size = $size ?? 100;
        $this->predicate = $predicate ?? fn(int $value): bool => $value >= $this->lowerBound && $value <= $this->upperBound;
    }

    /**
     * @psalm-pure
     */
    public static function any(): self
    {
        return new self(\PHP_INT_MIN, \PHP_INT_MAX);
    }

    /**
     * @psalm-pure
     */
    public static function between(int $lowerBound, int $upperBound): self
    {
        return new self($lowerBound, $upperBound);
    }

    /**
     * @psalm-pure
     */
    public static function above(int $lowerBound): self
    {
        return new self($lowerBound, \PHP_INT_MAX);
    }

    /**
     * @psalm-pure
     */
    public static function below(int $upperBound): self
    {
        return new self(\PHP_INT_MIN, $upperBound);
    }

    /**
     * @psalm-mutation-free
     */
    public function lowerBound(): int
    {
        return $this->lowerBound;
    }

    /**
     * @psalm-mutation-free
     */
    public function take(int $size): self
    {
        return new self(
            $this->lowerBound,
            $this->upperBound,
            $size,
            $this->predicate,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function filter(callable $predicate): self
    {
        $previous = $this->predicate;

        return new self(
            $this->lowerBound,
            $this->upperBound,
            $this->size,
            static function(int $value) use ($previous, $predicate): bool {
                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

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
