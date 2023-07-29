<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Call
{
    /**
     * @template T
     *
     * @param callable(): T $call
     *
     * @return Set<T>
     */
    public static function of(callable $call): Set
    {
        return Integers::any()->map(static fn() => $call());
    }
}
