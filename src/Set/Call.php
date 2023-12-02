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
        return FromGenerator::mutable(static function() use ($call) {
            while (true) {
                yield $call;
            }
        })->map(static fn($call) => $call());
    }
}
