<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

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
    public function values(): \Generator
    {
        $immutable = $this->set->values()->current()->isImmutable();

        foreach ($this->sizes->values() as $size) {
            $values = $this->generate($size->unwrap());

            if (!($this->predicate)($this->wrap($values))) {
                continue;
            }

            if ($immutable) {
                yield Set\Value::immutable(
                    $this->wrap($values),
                    $this->shrink(false, $values),
                );
            } else {
                yield Set\Value::mutable(
                    fn() => $this->wrap($values),
                    $this->shrink(true, $values),
                );
            }
        }
    }

    /**
     * @return list<Value>
     */
    private function generate(int $size): array
    {
        /** @var list<Value> */
        return \iterator_to_array($this->set->take($size)->values());
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
     * @param list<Value<I>> $sequence
     */
    private function shrink(bool $mutable, array $sequence): ?Dichotomy
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
            return $this->identity($mutable, $sequence);
        }

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $this->wrap($shrinked),
                $this->shrink(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $this->wrap($shrinked),
            $this->shrink(false, $shrinked),
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
            return $this->identity($mutable, $sequence);
        }

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $this->wrap($shrinked),
                $this->shrink(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $this->wrap($shrinked),
            $this->shrink(false, $shrinked),
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
