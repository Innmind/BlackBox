<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class Detonate
{
    /**
     * @internal
     * @template A
     *
     * @param list<Value<A>> $sequence
     *
     * @return list<A>
     */
    public static function of(array $sequence): array
    {
        return \array_map(
            static fn($value) => $value->unwrap(),
            $sequence,
        );
    }
}
