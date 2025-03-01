<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 *
 * @template I
 * @implements Set<list<I>>
 */
final class Sequence implements Set
{
    /** @var Set<I> */
    private Set $set;
    private Integers $sizes;
    /** @var positive-int */
    private int $size;
    /** @var \Closure(list<I>): bool */
    private \Closure $predicate;

    /**
     * @psalm-mutation-free
     *
     * @param Set<I> $set
     * @param positive-int $size
     * @param \Closure(list<I>): bool $predicate
     */
    private function __construct(
        Set $set,
        Integers $sizes,
        ?int $size = null,
        ?\Closure $predicate = null,
    ) {
        $this->set = $set;
        $this->sizes = $sizes;
        $this->size = $size ?? 100;
        $this->predicate = $predicate ?? static fn(array $sequence): bool => \count($sequence) >= $sizes->lowerBound();
    }

    /**
     * @psalm-pure
     *
     * @template U
     *
     * @param Set<U>|Provider<U> $set
     *
     * @return self<U>
     */
    public static function of(Set|Provider $set): self
    {
        return new self(Collapse::of($set), Integers::between(0, 100));
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     *
     * @return Set<list<I>>
     */
    public function atLeast(int $size): Set
    {
        return new self(
            $this->set,
            Integers::between($size, $size + 100),
            $this->size,
            null, // to make sure the lower bound is respected
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     *
     * @return Set<list<I>>
     */
    public function atMost(int $size): Set
    {
        return new self(
            $this->set,
            Integers::between(0, $size),
            $this->size,
            null, // to make sure the lower bound is respected
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param 0|positive-int $lower
     * @param positive-int $upper
     *
     * @return Set<list<I>>
     */
    public function between(int $lower, int $upper): Set
    {
        return new self(
            $this->set,
            Integers::between($lower, $upper),
            $this->size,
            null, // to make sure the lower bound is respected
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): Set
    {
        return new self(
            $this->set,
            $this->sizes->take($size),
            $size,
            $this->predicate,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;

        return new self(
            $this->set,
            $this->sizes,
            $this->size,
            static function(array $value) use ($previous, $predicate): bool {
                /** @var list<I> $value */
                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
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
        $immutable = $this->set->values($random)->current()?->isImmutable() ?? false;
        $yielded = 0;

        do {
            foreach ($this->sizes->values($random) as $size) {
                if ($yielded === $this->size) {
                    return;
                }

                /** @psalm-suppress ArgumentTypeCoercion */
                $values = $this->generate($size->unwrap(), $random);

                if (!($this->predicate)(Sequence\Detonate::of($values))) {
                    continue;
                }

                if ($immutable) {
                    yield Value::immutable(
                        Sequence\Detonate::of($values),
                        Sequence\RecursiveHalf::of(
                            false,
                            $this->predicate,
                            $values,
                        ),
                    );
                } else {
                    yield Value::mutable(
                        static fn() => Sequence\Detonate::of($values),
                        Sequence\RecursiveHalf::of(
                            true,
                            $this->predicate,
                            $values,
                        ),
                    );
                }

                ++$yielded;
            }
        } while ($yielded < $this->size);
    }

    /**
     * @param 0|positive-int $size
     *
     * @return list<Value<I>>
     */
    private function generate(int $size, Random $rand): array
    {
        if ($size === 0) {
            return [];
        }

        return \array_values(\iterator_to_array($this->set->take($size)->values($rand)));
    }
}
