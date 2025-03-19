<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Value;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};

/**
 * @internal
 * @template T
 */
interface Shrinker
{
    /**
     * @param Value<T> $value
     *
     * @return ?Dichotomy<T>
     */
    public function __invoke(Value $value): ?Dichotomy;
}
