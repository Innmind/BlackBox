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
     */
    private function __construct(
        private int $min,
        private int $max,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator {
        $min = $this->min;
        $max = $this->max;
        $bounds = static fn(int $value): bool => $value >= $min && $value <= $max;
        $predicate = static fn(int $value): bool => $bounds($value) && $predicate($value);
        $iterations = 0;

        while ($iterations < $size) {
            $value = $random->between($this->min, $this->max);
            $value = Value::of($value)
                ->predicatedOn($predicate);

            if (!$value->acceptable()) {
                continue;
            }

            yield $value->shrinkWith(Integers\Shrinker::instance);
            ++$iterations;
        }
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
}
