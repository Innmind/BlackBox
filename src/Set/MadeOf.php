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
final class MadeOf implements Set
{
    /** @var Set<string> */
    private Set $chars;

    /**
     * @no-named-arguments
     *
     * @param Set<string> $chars
     */
    private function __construct(Set $chars)
    {
        $this->chars = $chars;
    }

    /**
     * @no-named-arguments
     *
     * @param Set<string> $first
     * @param Set<string> $rest
     */
    public static function of(Set $first, Set ...$rest): self
    {
        $chars = $first;

        if (\count($rest) > 0) {
            $chars = Set\Either::any($first, ...$rest);
        }

        return new self($chars);
    }

    /**
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
     * @param positive-int $length
     *
     * @return Set<string>
     */
    public function atLeast(int $length): Set
    {
        return $this->build($length, $length + 128);
    }

    /**
     * @param positive-int $length
     *
     * @return Set<string>
     */
    public function atMost(int $length): Set
    {
        return $this->build(0, $length);
    }

    public function take(int $size): Set
    {
        return $this->build(0, 128)->take($size);
    }

    public function filter(callable $predicate): Set
    {
        return $this->build(0, 128)->filter($predicate);
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        yield from $this
            ->build(0, 128)
            ->values($random);
    }

    /**
     * @param 0|positive-int $min
     * @param positive-int $max
     *
     * @return Set<string>
     */
    private function build(int $min, int $max): Set
    {
        return Sequence::of($this->chars)
            ->between($min, $max)
            ->map(static fn(array $chars) => \implode('', $chars));
    }
}
