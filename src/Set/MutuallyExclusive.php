<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class MutuallyExclusive
{
    /**
     * @psalm-pure
     * @no-named-arguments
     *
     * @param Set<string>|Provider<string> $first
     * @param Set<string>|Provider<string> $second
     * @param Set<string>|Provider<string> $rest
     *
     * @return Set<non-empty-list<string>>
     */
    #[\NoDiscard]
    public static function of(
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$rest,
    ): Set {
        /** @var Set<non-empty-list<string>> */
        return Set::tuple(
            $first,
            $second,
            ...$rest,
        )->filter(static function($strings) {
            foreach ($strings as $i => $a) {
                foreach ($strings as $j => $b) {
                    if ($i === $j) {
                        continue;
                    }

                    if (\str_contains(\strtolower($a), \strtolower($b))) {
                        return false;
                    }
                }
            }

            return true;
        });
    }
}
