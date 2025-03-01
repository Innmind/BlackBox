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
 * @implements Provider<Ensure>
 */
final class Properties implements Set, Provider
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
     * @param Set<Concrete>|Provider<Concrete> $first
     * @param Set<Concrete>|Provider<Concrete> $properties
     */
    public static function any(Set|Provider $first, Set|Provider ...$properties): self
    {
        if (\count($properties) === 0) {
            return new self(Collapse::of($first));
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
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this->toSet());
    }

    /**
     * @return \Generator<Value<Ensure>>
     */
    #[\Override]
    public function values(Random $random): \Generator
    {
        yield from $this->toSet()->values($random);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<Ensure>
     */
    #[\Override]
    public function toSet(): Set
    {
        return $this->ensure(100);
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
