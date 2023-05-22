<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Random,
    Set,
    Property as Concrete,
    Properties as Ensure,
};

/**
 * @implements Set<Ensure>
 */
final class Properties implements Set
{
    /** @var Set<Concrete> */
    private Set $properties;
    private Integers $range;
    /** @var Set<Ensure> */
    private Set $ensure;

    /**
     * @param Set<Concrete> $properties
     */
    private function __construct(
        Set $properties,
        Integers $range,
    ) {
        $this->properties = $properties;
        $this->range = $range;

        /** @var Set<list<Concrete>> */
        $sequences = Sequence::of($properties, $range);

        /**
         * @psalm-suppress MixedArgument
         * @psalm-suppress InvalidArgument
         */
        $this->ensure = Decorate::immutable(
            static fn(array $properties): Ensure => new Ensure(...\array_values($properties)),
            $sequences,
        );
    }

    /**
     * @no-named-arguments
     *
     * @param Set<Concrete> $first
     * @param Set<Concrete> $properties
     */
    public static function any(Set $first, Set ...$properties): self
    {
        if (\count($properties) === 0) {
            return new self($first, Integers::between(1, 100));
        }

        return new self(
            Either::any($first, ...$properties),
            Integers::between(1, 100),
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $max
     */
    public function atMost(int $max): self
    {
        return new self(
            $this->properties,
            Integers::between(1, $max),
        );
    }

    /**
     * @return Set<Ensure>
     */
    public function take(int $size): Set
    {
        return $this->ensure->take($size);
    }

    /**
     * @param callable(Ensure): bool $predicate
     *
     * @return Set<Ensure>
     */
    public function filter(callable $predicate): Set
    {
        return $this->ensure->filter($predicate);
    }

    /**
     * @return \Generator<Value<Ensure>>
     */
    public function values(Random $random): \Generator
    {
        yield from $this->ensure->values($random);
    }
}
