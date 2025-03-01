<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * Use this set to prevent iterating over all possible combinations of a composite set
 *
 * It will allow to test more diverse combinations for a given set
 *
 * @template I
 * @implements Set<I>
 */
final class Randomize implements Set
{
    /** @var Set<I> */
    private Set $set;
    /** @var positive-int */
    private int $size;

    /**
     * @psalm-mutation-free
     *
     * @param Set<I> $set
     * @param positive-int $size
     */
    private function __construct(Set $set, int $size)
    {
        $this->set = $set;
        $this->size = $size;
    }

    /**
     * @psalm-pure
     *
     * @template T
     *
     * @param Set<T>|Provider<T> $set
     *
     * @return self<T>
     */
    public static function of(Set|Provider $set): self
    {
        return new self(Collapse::of($set), 100);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): Set
    {
        return new self(
            $this->set,
            $size,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): Set
    {
        return new self(
            $this->set->filter($predicate),
            $this->size,
        );
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
        $iterations = 0;

        while ($iterations < $this->size) {
            try {
                $value = $this->set->values($random)->current();
            } catch (EmptySet $e) {
                continue;
            }

            if (\is_null($value)) {
                continue;
            }

            yield $value;
            ++$iterations;
        }
    }
}
