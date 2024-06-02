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
        int $size = null,
        \Closure $predicate = null,
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
     * @param Set<U> $set
     *
     * @return self<U>
     */
    public static function of(Set $set): self
    {
        return new self($set, Integers::between(0, 100));
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
    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        $immutable = $this->set->values($random)->current()->isImmutable();
        $yielded = 0;

        do {
            foreach ($this->sizes->values($random) as $size) {
                if ($yielded === $this->size) {
                    return;
                }

                /** @psalm-suppress ArgumentTypeCoercion */
                $values = $this->generate($size->unwrap(), $random);

                if (!($this->predicate)($this->wrap($values))) {
                    continue;
                }

                if ($immutable) {
                    yield Value::immutable(
                        $this->wrap($values),
                        $this->shrinkFast(false, $values),
                    );
                } else {
                    yield Value::mutable(
                        fn() => $this->wrap($values),
                        $this->shrinkFast(true, $values),
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

    /**
     * @param list<Value<I>> $values
     *
     * @return list<I>
     */
    private function wrap(array $values): array
    {
        return \array_map(
            static fn(Value $value) => $value->unwrap(),
            $values,
        );
    }

    /**
     * The shrinking starts here and recursively will do:
     * - remove the half end of the sequence (while it breaks the test)
     * - remove the last element (while it breaks the test)
     * - remove the head element (while it breaks the test)
     * - shrink elements with their A strategy (while it breaks the test)
     * - shrink elements with their B strategy (while it breaks the test)
     *
     * @param list<Value<I>> $sequence
     */
    private function shrinkFast(bool $mutable, array $sequence): ?Dichotomy
    {
        if (\count($sequence) === 0) {
            return null;
        }

        if (!($this->predicate)($this->wrap($sequence))) {
            return null;
        }

        return new Dichotomy(
            $this->removeHalfTheStructure($mutable, $sequence),
            $this->removeTailElement($mutable, $sequence),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     */
    private function shrinkEnds(bool $mutable, array $sequence): ?Dichotomy
    {
        if (\count($sequence) === 0) {
            return null;
        }

        if (!($this->predicate)($this->wrap($sequence))) {
            return null;
        }

        return new Dichotomy(
            $this->removeTailElement($mutable, $sequence),
            $this->removeHeadElement($mutable, $sequence),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     */
    private function shrinkDecoratedWithStrategyA(bool $mutable, array $sequence): ?Dichotomy
    {
        if (\count($sequence) === 0) {
            return null;
        }

        if (!($this->predicate)($this->wrap($sequence))) {
            return null;
        }

        return new Dichotomy(
            $this->removeHeadElement($mutable, $sequence),
            $this->shrinkValuesWithStrategyA($mutable, $sequence),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     */
    private function shrinkDecoratedWithStrategyB(bool $mutable, array $sequence): ?Dichotomy
    {
        if (\count($sequence) === 0) {
            return null;
        }

        if (!($this->predicate)($this->wrap($sequence))) {
            return null;
        }

        return new Dichotomy(
            $this->shrinkValuesWithStrategyA($mutable, $sequence),
            $this->shrinkValuesWithStrategyB($mutable, $sequence),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     *
     * @return callable(): Value<list<I>>
     */
    private function removeHalfTheStructure(bool $mutable, array $sequence): callable
    {
        // we round half down otherwise a sequence of 1 element would be shrunk
        // to a sequence of 1 element resulting in a infinite recursion
        $numberToKeep = (int) \round(\count($sequence) / 2, 0, \PHP_ROUND_HALF_DOWN);
        $shrinked = \array_slice($sequence, 0, $numberToKeep);

        if (!($this->predicate)($this->wrap($shrinked))) {
            return $this->removeTailElement($mutable, $sequence);
        }

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $this->wrap($shrinked),
                $this->shrinkFast(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $this->wrap($shrinked),
            $this->shrinkFast(false, $shrinked),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     *
     * @return callable(): Value<list<I>>
     */
    private function removeTailElement(bool $mutable, array $sequence): callable
    {
        $shrinked = $sequence;
        \array_pop($shrinked);

        if (!($this->predicate)($this->wrap($shrinked))) {
            return $this->removeHeadElement($mutable, $sequence);
        }

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $this->wrap($shrinked),
                $this->shrinkEnds(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $this->wrap($shrinked),
            $this->shrinkEnds(false, $shrinked),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     *
     * @return callable(): Value<list<I>>
     */
    private function removeHeadElement(bool $mutable, array $sequence): callable
    {
        $shrinked = $sequence;
        \array_shift($shrinked);

        if (!($this->predicate)($this->wrap($shrinked))) {
            return $this->shrinkValuesWithStrategyA($mutable, $sequence);
        }

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $this->wrap($shrinked),
                $this->shrinkDecoratedWithStrategyA(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $this->wrap($shrinked),
            $this->shrinkDecoratedWithStrategyA(false, $shrinked),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     *
     * @return callable(): Value<list<I>>
     */
    private function shrinkValuesWithStrategyA(bool $mutable, array $sequence): callable
    {
        $reversed = \array_reverse($sequence);
        $shrinked = [];
        $shrinkedOne = false;

        foreach ($reversed as $value) {
            if (!$shrinkedOne && $value->shrinkable()) {
                $shrinked[] = $value->shrink()->a();
                $shrinkedOne = true;
            } else {
                $shrinked[] = $value;
            }
        }

        if (!$shrinkedOne) {
            return $this->identity($mutable, $sequence);
        }

        $shrinked = \array_reverse($shrinked);

        if (!($this->predicate)($this->wrap($shrinked))) {
            return $this->shrinkValuesWithStrategyB($mutable, $sequence);
        }

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $this->wrap($shrinked),
                $this->shrinkDecoratedWithStrategyB(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $this->wrap($shrinked),
            $this->shrinkDecoratedWithStrategyB(false, $shrinked),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     *
     * @return callable(): Value<list<I>>
     */
    private function shrinkValuesWithStrategyB(bool $mutable, array $sequence): callable
    {
        $reversed = \array_reverse($sequence);
        $shrinked = [];
        $shrinkedOne = false;

        foreach ($reversed as $value) {
            if (!$shrinkedOne && $value->shrinkable()) {
                $shrinked[] = $value->shrink()->a();
                $shrinkedOne = true;
            } else {
                $shrinked[] = $value;
            }
        }

        if (!$shrinkedOne) {
            return $this->identity($mutable, $sequence);
        }

        $shrinked = \array_reverse($shrinked);

        if (!($this->predicate)($this->wrap($shrinked))) {
            return $this->identity($mutable, $sequence);
        }

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $this->wrap($shrinked),
                $this->shrinkEnds(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $this->wrap($shrinked),
            $this->shrinkEnds(false, $shrinked),
        );
    }

    /**
     * @param list<Value<I>> $sequence
     *
     * @return callable(): Value<list<I>>
     */
    private function identity(bool $mutable, array $sequence): callable
    {
        if ($mutable) {
            return fn(): Value => Value::mutable(fn() => $this->wrap($sequence));
        }

        return fn(): Value => Value::immutable($this->wrap($sequence));
    }
}
