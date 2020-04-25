<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Property,
    Properties as Ensure,
};

final class Properties
{
    /**
     * @return Set<Ensure>
     */
    public static function of(Property $first, Property ...$properties): Set
    {
        /** @var Set<list<Property>> */
        $sequences = Sequence::of(
            Elements::of($first, ...$properties),
            Integers::between(1, 100), // at least one property must be chosen
        );

        /** @psalm-suppress MixedArgument */
        return Decorate::immutable(
            static fn(array $properties): Ensure => new Ensure(...$properties),
            $sequences,
        );
    }
}
