<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Property as Concrete,
};

final class Property
{
    /**
     * @no-named-arguments
     *
     * @param class-string<Concrete> $property
     *
     * @return Set<Concrete>
     */
    public static function of(string $property, Set ...$inputs): Set
    {
        $count = \count($inputs);

        if ($count === 0) {
            return Elements::of(new $property);
        }

        if ($count === 1) {
            return Decorate::immutable(
                static fn($input): Concrete => new $property($input),
                \reset($inputs),
            );
        }

        /** @psalm-suppress InvalidArgument */
        return Composite::immutable(
            static fn(...$inputs): Concrete => new $property(...$inputs),
            ...$inputs,
        );
    }
}
