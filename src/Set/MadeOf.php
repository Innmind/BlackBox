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
    /** @var non-empty-list<Set<string>> */
    private array $sets;
    private Integers $range;
    private int $size = 100;
    /** @var \Closure(string): bool */
    private \Closure $predicate;

    /**
     * @no-named-arguments
     *
     * @param Set<string> $first
     * @param Set<string> $rest
     */
    private function __construct(Set $first, Set ...$rest)
    {
        $this->sets = [$first, ...$rest];
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
        return new self($first, ...$rest);
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
        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;
        $self = clone $this;
        $self->predicate = static function(string $value) use ($previous, $predicate): bool {
            /** @psalm-suppress MixedArgumentTypeCoercion */
            if (!$previous($value)) {
                return false;
            }

            /** @psalm-suppress MixedArgumentTypeCoercion */
            return $predicate($value);
        };

        return $self;
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(Random $random): \Generator
    {
        $chars = $this->sets[0];

        if (\count($this->sets) > 1) {
            $chars = Set\Either::any(...$this->sets);
        }

        /**
         * @psalm-suppress MixedArgumentTypeCoercion Due to array not being a list
         * @psalm-suppress InvalidArgument Same problem as above
         * @var Set<string>
         */
        $set = Sequence::of($chars, $this->range)->map(
            static fn(array $chars): string => \implode('', $chars),
        );

        yield from $set
            ->take($this->size)
            ->filter($this->predicate)
            ->values($random);
    }
}
