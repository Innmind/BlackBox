<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * Use this set to prove your code is indifferent to the value passed to it
 */
final class Type
{
    /**
     * @return Set<mixed>
     */
    public static function any(): Set
    {
        return Set::either(
            self::primitives(),
            Set::sequence(self::primitives())->between(0, 1), // no more needed to prove type indifference
            Set::sequence(self::primitives())
                ->between(0, 1) // no more needed to prove type indifference
                ->map(static fn(array $array): \Iterator => new \ArrayIterator($array)),
        );
    }

    /**
     * @return Set<mixed>
     */
    private static function primitives(): Set
    {
        // no resource is generated as it may result in a fatal error of too
        // many opened resources
        /**
         * @psalm-suppress InvalidArgument Don't why it complains
         */
        return Set::either(
            Set::of(true, false, null),
            Set::integers(),
            Set::realNumbers(),
            Unicode::strings(),
            Set::generator(static function() { // objects
                while (true) {
                    yield new class {
                    };
                }
            }),
            Set::generator(static function() { // callables
                while (true) {
                    yield new class {
                        public function __invoke()
                        {
                        }
                    };
                    yield static fn() => null;
                    yield static fn() => null;
                }
            }),
        );
    }
}
