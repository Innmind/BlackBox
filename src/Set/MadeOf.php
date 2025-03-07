<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Provider<string>
 */
final class MadeOf implements Provider
{
    /** @var Set<string>|Provider<string> */
    private Set|Provider $chars;

    /**
     * @psalm-mutation-free
     *
     * @param Set<string>|Provider<string> $chars
     */
    private function __construct(Set|Provider $chars)
    {
        $this->chars = $chars;
    }

    /**
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @param Set<string>|Provider<string> $first
     * @param Set<string>|Provider<string> $rest
     */
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
     * @param 0|positive-int $minLength
     * @param positive-int $maxLength
     *
     * @return Set<string>
     */
    public function between(int $minLength, int $maxLength): Set
    {
        return $this->build($minLength, $maxLength);
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $length
     *
     * @return Set<non-empty-string>
     */
    public function atLeast(int $length): Set
    {
        /** @var Set<non-empty-string> */
        return $this->build($length, $length + 128);
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $length
     *
     * @return Set<string>
     */
    public function atMost(int $length): Set
    {
        return $this->build(0, $length);
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     *
     * @return Set<string>
     */
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
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(string): (V|Set\Seed<V>) $map
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
     * @template V
     *
     * @param callable(Seed<string>): (Set<V>|Provider<V>) $map
     *
     * @return Set<V>
     */
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    #[\Override]
    public function toSet(): Set
    {
        return $this->build(0, 128);
    }

    /**
     * @psalm-mutation-free
     *
     * @param 0|positive-int $min
     * @param positive-int $max
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
