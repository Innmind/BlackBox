<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
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
        throw new \LogicException('Either set can\'t be filtered, underlying data must be filtered beforehand');
    }

    public function values(Random $rand): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            $setToChoose = $rand(0, \count($this->sets) - 1);

            $value = $this->sets[$setToChoose]->values($rand)->current();

            /**
             * $value can be sometime null when the underlying set generate an
             * empty set, it may be the case when someones an aggressive filter
             * leading the system having a hard time generating values
             * @psalm-suppress DocblockTypeContradiction
             */
            if (!$value instanceof Value) {
                continue;
            }

            yield $value;
            ++$iterations;
        }
    }
}
