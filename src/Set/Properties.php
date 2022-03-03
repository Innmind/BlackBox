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
     * @no-named-arguments
     *
     * @return Set<Ensure>
     */
    public static function of(Concrete $first, Concrete ...$properties): Set
    {
        return self::chooseFrom(
            Elements::of($first, ...$properties),
        );
    }

    /**
     * @no-named-arguments
     *
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
    public static function chooseFrom(Set $set, Integers $range = null): Set
    {
        $range ??= Integers::between(1, 100);

        if ($range->lowerBound() < 1) {
            throw new \LogicException('At least one property is required');
        }

        /** @var Set<list<Concrete>> */
        $sequences = Sequence::of($set, $range);

        /**
         * @psalm-suppress MixedArgument
         * @psalm-suppress InvalidArgument
         */
        return Decorate::immutable(
            static fn(array $properties): Ensure => new Ensure(...\array_values($properties)),
            $sequences,
        );
    }
}
