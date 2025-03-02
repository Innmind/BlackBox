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
        return Set::generator(static function() use ($call) {
            while (true) {
                yield $call;
            }
        })
            ->mutable()
            ->map(static fn($call) => $call());
    }
}
