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
     * @param int<1, max> $size
     */
    private function __construct(
        private int $min,
        private int $max,
        private int $size,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function implementation(?int $min, ?int $max): self
    {
        return new self(
            $min ?? \PHP_INT_MIN,
            $max ?? \PHP_INT_MAX,
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
            $size,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $min = $this->min;
        $max = $this->max;
        $bounds = static fn(float $value): bool => $value >= $min && $value <= $max;
        $predicate = static fn(float $value): bool => $bounds($value) && $predicate($value);
        $iterations = 0;

        while ($iterations < $this->size) {
            // simulate the function lcg_value()
            $lcg = ($random->between(0, 100) / 100);
            /** @psalm-suppress InvalidOperand Don't know why it complains */
            $value = $random->between($this->min, $this->max) * $lcg;
            $value = Value::of($value)
                ->predicatedOn($predicate);

            if (!$value->acceptable()) {
                continue;
            }

            yield $value->shrinkWith(RealNumbers\Shrinker::instance);
            ++$iterations;
        }
    }
}
