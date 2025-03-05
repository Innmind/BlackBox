<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @internal
 * @implements Implementation<float>
 */
final class RealNumbers implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(float): bool $predicate
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
            static fn(float $value): bool => $value >= $min && $value <= $max,
            100,
        );
    }

    /**
     * @deprecated Use Set::realNumbers() instead
     * @psalm-pure
     *
     * @return Set<float>
     */
    public static function any(): Set
    {
        return Set::realNumbers()->toSet();
    }

    /**
     * @deprecated Use Set::realNumbers() instead
     * @psalm-pure
     *
     * @return Set<float>
     */
    public static function between(int $min, int $max): Set
    {
        return Set::realNumbers()
            ->between($min, $max)
            ->toSet();
    }

    /**
     * @deprecated Use Set::realNumbers() instead
     * @psalm-pure
     *
     * @return Set<float>
     */
    public static function above(int $min): Set
    {
        return Set::realNumbers()
            ->above($min)
            ->toSet();
    }

    /**
     * @deprecated Use Set::realNumbers() instead
     * @psalm-pure
     *
     * @return Set<float>
     */
    public static function below(int $max): Set
    {
        return Set::realNumbers()
            ->below($max)
            ->toSet();
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
            static function(float $value) use ($previous, $predicate): bool {
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
            // simulate the function lcg_value()
            $lcg = ($random->between(0, 100) / 100);
            /** @psalm-suppress InvalidOperand Don't know why it complains */
            $value = $random->between($this->min, $this->max) * $lcg;

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
