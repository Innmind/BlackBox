<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

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
    ): \Generator {
        $min = $this->min;
        $max = $this->max;
        $bounds = static fn(int $value): bool => $value >= $min && $value <= $max;
        $predicate = static fn(int $value): bool => $bounds($value) && $predicate($value);

        while (true) {
            $value = $random->between($this->min, $this->max);

            yield Value::of($value)
                ->predicatedOn($predicate)
                ->shrinkWith(Integers\Shrinker::instance);
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
     * @psalm-mutation-free
     */
    public function min(): int
    {
        return $this->min;
    }
}
