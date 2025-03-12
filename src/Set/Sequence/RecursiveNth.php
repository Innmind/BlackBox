<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};

/**
 * @internal
 */
final class RecursiveNth
{
    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param positive-int $n
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function of(Value $value, int $n = 1): ?Dichotomy
    {
        if (\count($value->unwrap()) === 0) {
            return null;
        }

        if (!$value->map(Detonate::of(...))->acceptable()) {
            return null;
        }

        return new Dichotomy(
            RemoveNth::of($value, $n),
            RemoveNth::of($value, $n + 1),
        );
    }
}
