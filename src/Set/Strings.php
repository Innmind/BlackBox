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

    public function __construct(int $boundA = null, int $boundB = null)
    {
        // this trick is here because historically only the maxLength was configurable
        // with the first argument
        $boundA ??= 128;
        $boundB ??= 0;

        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         */
        $this->set = Decorate::immutable(
            static fn(array $chars): string => \implode('', $chars),
            Sequence::of(
                Chars::any(),
                Integers::between(\min($boundA, $boundB), \max($boundA, $boundB)),
            ),
        );
    }

    public static function any(int $maxLength = null): self
    {
        if (\is_int($maxLength)) {
            \trigger_error('Use Strings::atMost() instead', \E_USER_DEPRECATED);
        }

        return new self($maxLength);
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
        return new MadeOf($first, ...$rest);
    }

    public function take(int $size): Set
    {
        return $this->set->take($size);
    }

    public function filter(callable $predicate): Set
    {
        return $this->set->filter($predicate);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(Random $rand): \Generator
    {
        yield from $this->set->values($rand);
    }
}
