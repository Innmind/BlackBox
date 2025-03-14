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
            static fn(float $value) => $previous($value) && $predicate($value),
            $this->size,
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
     * @param Value<float> $value
     *
     * @return Dichotomy<float>|null
     */
    private static function shrink(Value $value): ?Dichotomy
    {
        if (\round($value->unwrap(), 5) === 0.0) {
            return null;
        }

        return new Dichotomy(
            self::divideByTwo($value),
            self::reduceByOne($value),
        );
    }

    /**
     * @param Value<float> $value
     *
     * @return callable(): Value<float>
     */
    private static function divideByTwo(Value $value): callable
    {
        $shrunk = $value->map(static fn(float $value) => $value / 2.0);

        if (!$shrunk->acceptable()) {
            return self::reduceByOne($value);
        }

        return static fn(): Value => $shrunk->shrinkWith(self::shrink($shrunk));
    }

    /**
     * @param Value<float> $value
     *
     * @return callable(): Value<float>
     */
    private static function reduceByOne(Value $value): callable
    {
        // add one when the value is negative, otherwise subtract one
        /** @psalm-suppress InvalidOperand Don't know why it complains */
        $shrunk = $value->map(static fn(float $value) => $value + (
            ($value <=> 0.0) * -1.0
        ));

        if (!$shrunk->acceptable()) {
            return static fn() => $value->withoutShrinking();
        }

        return static fn(): Value => $shrunk->shrinkWith(self::shrink($shrunk));
    }
}
