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
     * @param Set<string> $first
     * @param Set<string> $second
     * @param Set<string> $rest
     *
     * @return Set<list<string>>
     */
    public static function of(
        Set $first,
        Set $second,
        Set ...$rest,
    ): Set {
        /** @var Set<list<string>> */
        return Composite::immutable(
            static fn(string ...$args) => $args,
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
