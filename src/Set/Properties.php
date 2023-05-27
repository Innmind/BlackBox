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

        /** @var Set<non-empty-list<Concrete>> */
        $sequences = Sequence::of($properties, $range);

        $this->ensure = $sequences->map(
            static fn(array $properties): Ensure => new Ensure(...$properties),
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

    public function take(int $size): Set
    {
        return $this->ensure->take($size);
    }

    public function filter(callable $predicate): Set
    {
        return $this->ensure->filter($predicate);
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this->ensure);
    }

    /**
     * @return \Generator<Value<Ensure>>
     */
    public function values(Random $random): \Generator
    {
        yield from $this->ensure->values($random);
    }
}
