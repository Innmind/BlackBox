<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @implements Set<string>
 */
final class Strings implements Set
{
    /** @var Set<string> */
    private Set $set;

    private function __construct(int $min, int $max)
    {
        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         */
        $this->set = Sequence::of(
            Chars::any(),
            Integers::between($min, $max),
        )->map(static fn(array $chars): string => \implode('', $chars));
    }

    public static function any(): self
    {
        return new self(0, 128);
    }

    public static function between(int $minLength, int $maxLength): self
    {
        return new self($minLength, $maxLength);
    }

    public static function atMost(int $maxLength): self
    {
        return new self(0, $maxLength);
    }

    public static function atLeast(int $minLength): self
    {
        return new self($minLength, $minLength + 128);
    }

    /**
     * @no-named-arguments
     *
     * @param Set<string> $first
     * @param Set<string> $rest
     */
    public static function madeOf(Set $first, Set ...$rest): MadeOf
    {
        return MadeOf::of($first, ...$rest);
    }

    public function take(int $size): Set
    {
        return $this->set->take($size);
    }

    public function filter(callable $predicate): Set
    {
        return $this->set->filter($predicate);
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this->set);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(Random $random): \Generator
    {
        yield from $this->set->values($random);
    }
}
