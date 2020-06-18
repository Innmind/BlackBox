<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * {@inheritdoc}
 * @template I
 */
final class Sequence implements Set
{
    private Set $set;
    /** @var Set<int> */
    private Set $sizes;
    /** @var callable(list<I>): bool */
    private \Closure $predicate;

    /**
     * @param Set<I> $set
     */
    private function __construct(Set $set, Integers $sizes = null)
    {
        $sizes ??= $sizes ?? Integers::between(0, 100);
        $this->set = $set;
        $this->sizes = $sizes->take(100);
        $this->predicate = static fn(array $sequence): bool => \count($sequence) >= $sizes->lowerBound();
    }

    /**
     * @template U
     *
     * @param Set<U> $set
     *
     * @return self<U>
     */
    public static function of(Set $set, Integers $sizes = null): self
    {
        return new self($set, $sizes);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->sizes = $this->sizes->take($size);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;
        $self = clone $this;
        $self->predicate = function(array $value) use ($previous, $predicate): bool {
            /** @psalm-suppress MixedArgumentTypeCoercion */
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * @return \Generator<Set\Value<list<I>>>
     */
    public function values(Random $rand): \Generator
    {
        $immutable = $this->set->values($rand)->current()->isImmutable();

        foreach ($this->sizes->values($rand) as $size) {
            $values = $this->generate($size->unwrap(), $rand);

            if (!($this->predicate)($this->wrap($values))) {
                continue;
            }

            if ($immutable) {
                yield Set\Value::immutable(
                    $this->wrap($values),
                    $this->shrinkFast(false, $values),
                );
            } else {
                yield Set\Value::mutable(
                    fn() => $this->wrap($values),
                    $this->shrinkFast(true, $values),
                );
            }
        }
    }

    /**
     * @return list<Value>
     */
    private function generate(int $size, Random $rand): array
    {
        /** @var list<Value> */
        return \iterator_to_array($this->set->take($size)->values($rand));
    }

    /**
     * @param list<Value> $values
     *
     * @return list<I>
     */
    private function wrap(array $values): array
    {
        /** @psalm-suppress MissingClosureReturnType */
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
