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
     * @param Set<A>|Provider<A> $first
     * @param Set<B>|Provider<B> $second
     * @param Set<C>|Provider<C> $rest
     *
     * @return Set<non-empty-list<A|B|C>>
     */
    public static function of(
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$rest,
    ): Set {
        /** @var Set<non-empty-list<A|B|C>> */
        return Set::compose(
            static fn(mixed ...$args) => $args,
            $first,
            $second,
            ...$rest,
        )
            ->immutable()
            ->toSet();
    }
}
