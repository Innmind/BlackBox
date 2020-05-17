<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Property as Concrete,
    Properties as Ensure,
};

final class Properties
{
    /**
     * @return Set<Ensure>
     */
    public static function of(Concrete $first, Concrete ...$properties): Set
    {
        return self::chooseFrom(
            Elements::of($first, ...$properties),
        );
    }

    /**
     * @param Set<Concrete> $first
     * @param Set<Concrete> $properties
     *
     * @return Set<Ensure>
     */
    public static function any(Set $first, Set ...$properties): Set
    {
        if (\count($properties) === 0) {
            $set = $first;
        } else {
            $set = new Either($first, ...$properties);
        }

        return self::chooseFrom($set);
    }

    /**
     * @param Set<Concrete> $set
     *
     * @return Set<Ensure>
     */
    public static function chooseFrom(Set $set): Set
    {
        /** @var Set<list<Concrete>> */
        $sequences = Sequence::of(
            $set,
            Integers::between(1, 100), // at least one property must be chosen
        );

        /** @psalm-suppress MixedArgument */
        return Decorate::immutable(
            static fn(array $properties): Ensure => new Ensure(...$properties),
            $sequences,
        );
    }
}
