<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Seed,
    Set\Implementation,
};

/**
 * @implements Provider<string>
 */
final class Strings implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<string>): Set<string> $wrap
     */
    private function __construct(
        private \Closure $wrap,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param pure-Closure(Implementation<string>): Set<string> $wrap
     */
    public static function of(\Closure $wrap): self
    {
        return new self($wrap);
    }

    /**
     * @psalm-mutation-free
     */
    public function chars(): Strings\Chars
    {
        return Strings\Chars::of();
    }

    /**
     * @psalm-mutation-free
     */
    public function unicode(): Strings\Unicode
    {
        return Strings\Unicode::of();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function unsafe(): Set
    {
        return ($this->wrap)(Set\UnsafeStrings::implementation());
    }

    /**
     * @psalm-mutation-free
     *
     * @no-named-arguments
     *
     * @param Set<string>|Provider<string> $first
     * @param Set<string>|Provider<string> $rest
     */
    public function madeOf(Set|Provider $first, Set|Provider ...$rest): Set\MadeOf
    {
        return Set\MadeOf::of($first, ...$rest);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<0, max> $min
     * @param int<1, max> $max
     *
     * @return Set<string>
     */
    public function between(int $min, int $max): Set
    {
        return Set::sequence($this->chars())
            ->between($min, $max)
            ->map(static fn(array $chars): string => \implode('', $chars));
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $max
     *
     * @return Set<string>
     */
    public function atMost(int $max): Set
    {
        return $this->between(0, $max);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $min
     *
     * @return Set<string>
     */
    public function atLeast(int $min): Set
    {
        return $this->between($min, $min + 128);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
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
     */
    #[\Override]
    public function toSet(): Set
    {
        return $this->between(0, 128);
    }
}
