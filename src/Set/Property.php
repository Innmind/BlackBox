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
            /** @psalm-suppress MissingParamType */
            return Decorate::immutable(
                static fn($input): Concrete => new $property($input),
                \reset($inputs),
            );
        }

        return Composite::immutable(
            static fn(...$inputs): Concrete => new $property(...$inputs),
            ...$inputs,
        );
    }
}
