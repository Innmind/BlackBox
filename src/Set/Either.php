<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

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
            static function(Set $set) use ($size): Set {
                return $set->take($size);
            },
            $this->sets,
        );
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        throw new \LogicException('Either set can\'t be filtered, underlying data must be filtered beforehand');
    }

    public function values(): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            $setToChoose = \random_int(0, \count($this->sets) - 1);

            yield $this->sets[$setToChoose]->values()->current();
            ++$iterations;
        }
    }
}
