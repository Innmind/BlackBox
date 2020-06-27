<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * @implements Set<mixed>
 */
final class Either implements Set
{
    /** @var list<Set> */
    private array $sets;
    private int $size;

    public function __construct(Set $first, Set $second, Set ...$rest)
    {
        $this->sets = [$first, $second, ...$rest];
        $this->size = 100;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->sets = \array_map(
            static fn(Set $set): Set => $set->take($size),
            $this->sets,
        );
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->sets = \array_map(
            static fn(Set $set): Set => $set->filter($predicate),
            $this->sets,
        );

        return $self;
    }

    public function values(Random $rand): \Generator
    {
        $iterations = 0;
        $emptySets = [];

        while ($iterations < $this->size) {
            $setToChoose = $rand(0, \count($this->sets) - 1);

            try {
                $value = $this->sets[$setToChoose]->values($rand)->current();
            } catch (EmptySet $e) {
                $emptySets[$setToChoose] = null;

                if (\count($emptySets) === \count($this->sets)) {
                    throw new EmptySet;
                }

                continue;
            }

            yield $value;
            ++$iterations;
        }
    }
}
