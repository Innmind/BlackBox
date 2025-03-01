<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @implements Implementation<float>
 */
final class RealNumbers implements Implementation
{
    private int $lowerBound;
    private int $upperBound;
    /** @var positive-int */
    private int $size;
    /** @var \Closure(float): bool */
    private \Closure $predicate;

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     * @param \Closure(float): bool $predicate
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
        $this->predicate = $predicate ?? fn(float $value): bool => $value >= $this->lowerBound && $value <= $this->upperBound;
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
    #[\Override]
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
    #[\Override]
    public function filter(callable $predicate): self
    {
        $previous = $this->predicate;

        return new self(
            $this->lowerBound,
            $this->upperBound,
            $this->size,
            static function(float $value) use ($previous, $predicate): bool {
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
    #[\Override]
    public function map(callable $map): Implementation
    {
        return Decorate::immutable($map, Set::of($this));
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            // simulate the function lcg_value()
            $lcg = ($random->between(0, 100) / 100);
            /** @psalm-suppress InvalidOperand Don't know why it complains */
            $value = $random->between($this->lowerBound, $this->upperBound) * $lcg;

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
        /** @psalm-suppress InvalidOperand Don't know why it complains */
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
        /** @psalm-suppress InvalidOperand Don't know why it complains */
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
