<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\RealNumbers;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};

/**
 * @internal
 * @implements Value\Shrinker<float>
 */
enum Shrinker implements Value\Shrinker
{
    case instance;

    #[\Override]
    public function __invoke(Value $value): ?Dichotomy
    {
        if (\round($value->unwrap(), 5) === 0.0) {
            return null;
        }

        return Dichotomy::of(
            $this->divideByTwo($value),
            $this->reduceByOne($value),
        );
    }

    /**
     * @param Value<float> $value
     *
     * @return ?Value<float>
     */
    private function divideByTwo(Value $value): ?Value
    {
        $shrunk = $value->shrinkVia(static fn(float $value) => $value / 2.0);

        if (!$shrunk->acceptable()) {
            return $this->reduceByOne($value);
        }

        return $shrunk;
    }

    /**
     * @param Value<float> $value
     *
     * @return ?Value<float>
     */
    private function reduceByOne(Value $value): ?Value
    {
        // add one when the value is negative, otherwise subtract one
        /** @psalm-suppress InvalidOperand Don't know why it complains */
        $shrunk = $value->shrinkVia(static fn(float $value) => $value + (
            ($value <=> 0.0) * -1.0
        ));

        if (!$shrunk->acceptable()) {
            return null;
        }

        return $shrunk;
    }
}
