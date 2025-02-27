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
     * @psalm-mutation-free
     *
     * @no-named-arguments
     *
     * @param Set<string> $chars
     */
    private function __construct(Set $chars)
    {
        $this->chars = $chars;
    }

    /**
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @param Set<string> $first
     * @param Set<string> $rest
     */
    public static function of(Set $first, Set ...$rest): self
    {
        $chars = $first;

        if (\count($rest) > 0) {
            $chars = Either::any($first, ...$rest);
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
     * @return Set<string>
     */
    public function atLeast(int $length): Set
    {
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
     */
    #[\Override]
    public function take(int $size): Set
    {
        return $this->build(0, 128)->take($size);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): Set
    {
        return $this->build(0, 128)->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        yield from $this
            ->build(0, 128)
            ->values($random);
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
        return Sequence::of($this->chars)
            ->between($min, $max)
            ->map(static fn(array $chars) => \implode('', $chars));
    }
}
