<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider\Strings;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Seed,
    Exception\EmptySet,
};

/**
 * @implements Provider<string>
 */
final class MadeOf implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param Set<string>|Provider<string> $chars
     */
    private function __construct(private Set|Provider $chars)
    {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @param Set<string>|Provider<string> $first
     * @param Set<string>|Provider<string> $rest
     */
    #[\NoDiscard]
    public static function of(Set|Provider $first, Set|Provider ...$rest): self
    {
        $chars = $first;

        if (\count($rest) > 0) {
            return new self(Set::either($first, ...$rest));
        }

        return new self($chars);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<0, max> $minLength
     * @param int<1, max> $maxLength
     *
     * @return Set<string>
     */
    #[\NoDiscard]
    public function between(int $minLength, int $maxLength): Set
    {
        return $this->build($minLength, $maxLength);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $length
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public function atLeast(int $length): Set
    {
        /** @var Set<non-empty-string> */
        return $this->build($length, $length + 128);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $length
     *
     * @return Set<string>
     */
    #[\NoDiscard]
    public function atMost(int $length): Set
    {
        return $this->build(0, $length);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<string>
     */
    #[\NoDiscard]
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(string): bool $predicate
     *
     * @return Set<string>
     */
    #[\NoDiscard]
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(string): bool $predicate
     *
     * @return Set<string>
     */
    #[\NoDiscard]
    public function exclude(callable $predicate): Set
    {
        return $this->toSet()->exclude($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(string): V $map
     *
     * @return Set<V>
     */
    #[\NoDiscard]
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(Seed<string>): (Set<V>|Provider<V>) $map
     *
     * @return Set<V>
     */
    #[\NoDiscard]
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @template R
     *
     * @param Set<R>|Provider<R> $right
     *
     * @return Set<array{string, R}>
     */
    #[\NoDiscard]
    public function zip(Set|Provider $right): Set
    {
        return $this->toSet()->zip($right);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    #[\NoDiscard]
    public function randomize(): Set
    {
        return $this->toSet()->randomize();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<?string>
     */
    #[\NoDiscard]
    public function nullable(): Set
    {
        return $this->toSet()->nullable();
    }

    /**
     * @throws EmptySet When no value can be generated
     *
     * @return iterable<string>
     */
    #[\NoDiscard]
    public function enumerate(): iterable
    {
        return $this->toSet()->enumerate();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    #[\Override]
    #[\NoDiscard]
    public function toSet(): Set
    {
        return $this->build(0, 128);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<0, max> $min
     * @param int<1, max> $max
     *
     * @return Set<string>
     */
    private function build(int $min, int $max): Set
    {
        return Set::sequence($this->chars)
            ->between($min, $max)
            ->map(static fn(array $chars) => \implode('', $chars));
    }
}
