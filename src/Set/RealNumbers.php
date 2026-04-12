<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @implements Implementation<float>
 */
final class RealNumbers implements Implementation
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
        $bounds = static fn(float $value): bool => $value >= $min && $value <= $max;
        $predicate = static fn(float $value): bool => $bounds($value) && $predicate($value);

        while (true) {
            // simulate the function lcg_value()
            $lcg = ($random->between(0, 100) / 100);
            /** @psalm-suppress InvalidOperand Don't know why it complains */
            $value = $random->between($this->min, $this->max) * $lcg;

            if (!$bounds($value)) {
                continue;
            }

            yield Value::of($value)
                ->predicatedOn($predicate)
                ->shrinkWith(RealNumbers\Shrinker::instance);
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
}
