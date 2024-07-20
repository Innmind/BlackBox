<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Tuple
{
    /**
     * @psalm-pure
     * @no-named-arguments
     *
     * @template A
     * @template B
     * @template C
     *
     * @param Set<A> $first
     * @param Set<B> $second
     * @param Set<C> $rest
     *
     * @return Set<non-empty-list<A|B|C>>
     */
    public static function of(
        Set $first,
        Set $second,
        Set ...$rest,
    ): Set {
        /** @var Set<non-empty-list<A|B|C>> */
        return Composite::immutable(
            static fn(mixed ...$args) => $args,
            $first,
            $second,
            ...$rest,
        );
    }
}
