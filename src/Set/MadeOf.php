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
    private Integers $range;
    private int $size = 100;
    /** @var \Closure(string): bool */
    private \Closure $predicate;

    /**
     * @no-named-arguments
     *
     * @param Set<string> $chars
     */
    private function __construct(Set $chars)
    {
        $this->chars = $chars;
        $this->range = Integers::between(0, 128);
        $this->predicate = static fn(): bool => true;
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

    public function between(int $minLength, int $maxLength): self
    {
        $self = clone $this;
        $self->range = Integers::between($minLength, $maxLength);

        return $self;
    }

    public function atLeast(int $length): self
    {
        $self = clone $this;
        $self->range = Integers::between($length, $length + 128);

        return $self;
    }

    public function atMost(int $length): self
    {
        $self = clone $this;
        $self->range = Integers::between(0, $length);

        return $self;
    }

    public function take(int $size): Set
    {
        return $this->build()->take($size);
    }

    public function filter(callable $predicate): Set
    {
        return $this->build()->filter($predicate);
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        yield from $this
            ->build()
            ->values($random);
    }

    /**
     * @return Set<string>
     */
    private function build(): Set
    {
        return Sequence::of($this->chars, $this->range)->map(
            static fn(array $chars) => \implode('', $chars),
        );
    }
}
