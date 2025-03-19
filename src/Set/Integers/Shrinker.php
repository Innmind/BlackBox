<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Integers;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};

/**
 * @internal
 * @implements Value\Shrinker<int>
 */
enum Shrinker implements Value\Shrinker
{
    case instance;

    #[\Override]
    public function __invoke(Value $value): ?Dichotomy
    {
        if ($value->unwrap() === 0) {
            return null;
        }

        return Dichotomy::of(
            $this->divideByTwo($value),
            $this->reduceByOne($value),
        );
    }

    /**
     * @param Value<int> $value
     *
     * @return ?Value<int>
     */
    private function divideByTwo(Value $value): ?Value
    {
        $shrunk = $value->shrinkVia(static fn($int) => (int) \round(
            $int / 2,
            0,
            \PHP_ROUND_HALF_DOWN,
        ));

        if (!$shrunk->acceptable()) {
            return $this->reduceByOne($value);
        }

        return $shrunk;
    }

    /**
     * @param Value<int> $value
     *
     * @return ?Value<int>
     */
    private function reduceByOne(Value $value): ?Value
    {
        // add one when the value is negative, otherwise subtract one
        $shrunk = $value->shrinkVia(static fn($int) => $int + (($int <=> 0) * -1));

        if (!$shrunk->acceptable()) {
            return null;
        }

        return $shrunk;
    }
}
