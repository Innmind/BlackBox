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

    /**
     * @psalm-mutation-free
     *
     * @param Set<Concrete> $properties
     */
    private function __construct(Set $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @param Set<Concrete> $first
     * @param Set<Concrete> $properties
     */
    public static function any(Set $first, Set ...$properties): self
    {
        if (\count($properties) === 0) {
            return new self($first);
        }

        return new self(Either::any($first, ...$properties));
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $max
     *
     * @return Set<Ensure>
     */
    public function atMost(int $max): Set
    {
        return $this->ensure($max);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): Set
    {
        return $this->ensure(100)->take($size);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): Set
    {
        return $this->ensure(100)->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this->ensure(100));
    }

    /**
     * @return \Generator<Value<Ensure>>
     */
    #[\Override]
    public function values(Random $random): \Generator
    {
        yield from $this->ensure(100)->values($random);
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $max
     *
     * @return Set<Ensure>
     */
    private function ensure(int $max): Set
    {
        /** @var Set<non-empty-list<Concrete>> */
        $sequences = Sequence::of($this->properties)->between(1, $max);

        return $sequences->map(
            static fn(array $properties): Ensure => Ensure::of(...$properties),
        );
    }
}
