<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @internal
 * @implements Implementation<int>
 */
final class Integers implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(int): bool $predicate
     * @param int<1, max> $size
     */
    private function __construct(
        private int $min,
        private int $max,
        private \Closure $predicate,
        private int $size,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function implementation(?int $min, ?int $max): self
    {
        $min ??= \PHP_INT_MIN;
        $max ??= \PHP_INT_MAX;

        return new self(
            $min,
            $max,
            static fn(int $value): bool => $value >= $min && $value <= $max,
            100,
        );
    }

    /**
     * @deprecated Use Set::integers() instead
     * @psalm-pure
     *
     * @return Set<int>
     */
    public static function any(): Set
    {
        return Set::integers()->toSet();
    }

    /**
     * @deprecated Use Set::integers()->between() instead
     * @psalm-pure
     *
     * @return Set<int>
     */
    public static function between(int $min, int $max): Set
    {
        return Set::integers()
            ->between($min, $max)
            ->toSet();
    }

    /**
     * @deprecated Use Set::integers()->above() instead
     * @psalm-pure
     *
     * @return Set<int>
     */
    public static function above(int $min): Set
    {
        return Set::integers()
            ->above($min)
            ->toSet();
    }

    /**
     * @deprecated Use Set::integers()->below() instead
     * @psalm-pure
     *
     * @return Set<int>
     */
    public static function below(int $max): Set
    {
        return Set::integers()
            ->below($max)
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     */
    public function min(): int
    {
        return $this->min;
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->min,
            $this->max,
            $this->predicate,
            $size,
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
            $this->min,
            $this->max,
            static function(int $value) use ($previous, $predicate): bool {
                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
            $this->size,
        );
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            $value = $random->between($this->min, $this->max);
            $value = Value::immutable($value)
                ->predicatedOn($this->predicate);

            if (!$value->acceptable()) {
                continue;
            }

            yield $value->shrinkWith(self::shrink($value));
            ++$iterations;
        }
    }

    /**
     * @param Value<int> $value
     *
     * @return Dichotomy<int>|null
     */
    private static function shrink(Value $value): ?Dichotomy
    {
        if ($value->unwrap() === 0) {
            return null;
        }

        return new Dichotomy(
            self::divideByTwo($value),
            self::reduceByOne($value),
        );
    }

    /**
     * @param Value<int> $value
     *
     * @return callable(): Value<int>
     */
    private static function divideByTwo(Value $value): callable
    {
        $shrunk = $value->map(static fn($int) => (int) \round(
            $int / 2,
            0,
            \PHP_ROUND_HALF_DOWN,
        ));

        if (!$shrunk->acceptable()) {
            return self::reduceByOne($value);
        }

        return static fn(): Value => $shrunk->shrinkWith(self::shrink($shrunk));
    }

    /**
     * @param Value<int> $value
     *
     * @return callable(): Value<int>
     */
    private static function reduceByOne(Value $value): callable
    {
        // add one when the value is negative, otherwise subtract one
        $shrunk = $value->map(static fn($int) => $int + (($int <=> 0) * -1));

        if (!$shrunk->acceptable()) {
            return static fn() => $value->withoutShrinking();
        }

        return static fn(): Value => $shrunk->shrinkWith(self::shrink($shrunk));
    }
}
