<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Property as Concrete,
    Properties as Ensure,
};

/**
 * @implements Provider<Ensure>
 */
final class Properties implements Provider
{
    /** @var Set<Concrete>|Provider<Concrete> */
    private Set|Provider $properties;

    /**
     * @psalm-mutation-free
     *
     * @param Set<Concrete>|Provider<Concrete> $properties
     */
    private function __construct(Set|Provider $properties)
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
            return new self($first);
        }

        return new self(Set::either($first, ...$properties));
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
     *
     * @param positive-int $size
     *
     * @return Set<Ensure>
     */
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(Ensure): bool $predicate
     *
     * @return Set<Ensure>
     */
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(Ensure): V $map
     *
     * @return Set<V>
     */
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
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
