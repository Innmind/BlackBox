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

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Implementation
    {
        return Map::implementation($map, $this, true);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function flatMap(callable $map, callable $extract): Implementation
    {
        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         */
        return FlatMap::implementation(
            static fn($input) => $extract($map($input)),
            $this,
        );
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            $value = $random->between($this->min, $this->max);

            if (!($this->predicate)($value)) {
                continue;
            }

            yield Value::immutable($value)->shrinkWith($this->shrink($value));
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

        return fn(): Value => Value::immutable($shrinked)
            ->shrinkWith($this->shrink($shrinked));
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

        return fn(): Value => Value::immutable($shrinked)
            ->shrinkWith($this->shrink($shrinked));
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
