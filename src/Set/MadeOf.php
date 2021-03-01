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
    /** @var \Closure(list<I>): bool */
    private \Closure $predicate;

    /**
     * @param Set<string> $first
     * @param Set<string> $rest
     */
    public function __construct(Set $first, Set ...$rest)
    {
        $this->sets = [$first, ...$rest];
        $this->range = Integers::between(0, 128);
        $this->predicate = static fn(): bool => true;
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
        $self->predicate = static function(array $value) use ($previous, $predicate): bool {
            /** @psalm-suppress MixedArgumentTypeCoercion */
            if (!$previous($value)) {
                return false;
            }

            /** @psalm-suppress MixedArgumentTypeCoercion */
            return $predicate($value);
        };

        return $self;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(Random $rand): \Generator
    {
        $chars = $this->sets[0];

        if (\count($this->sets) > 1) {
            $chars = new Set\Either(...$this->sets);
        }

        $set = Decorate::immutable(
            static fn(array $chars): string => \implode('', $chars),
            Sequence::of($chars, $this->range),
        );

        yield from $set->values($rand);
    }
}
