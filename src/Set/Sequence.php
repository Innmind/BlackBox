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
    private Set $sizes;

    /**
     * @param Set<I> $set
     */
    private function __construct(Set $set, Set\Integers $sizes = null)
    {
        $this->set = $set;
        $this->sizes = ($sizes ?? Set\Integers::between(0, 100))->take(100);
    }

    /**
     * @param Set<I> $set
     *
     * @return Set<list<I>>
     */
    public static function of(Set $set, Set\Integers $sizes = null): self
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
        throw new \LogicException('Sequence set can\'t be filtered, underlying set must be filtered beforehand');
    }

    /**
     * @return \Generator<Set\Value<Structure<I>>>
     */
    public function values(): \Generator
    {
        $immutable = $this->set->values()->current()->isImmutable();

        foreach ($this->sizes->values() as $size) {
            $values = $this->generate($size->unwrap());

            if ($immutable) {
                yield Set\Value::immutable(
                    $this->wrap($values),
                    $this->shrink(false, $this->wrap($values)),
                );
            } else {
                yield Set\Value::mutable(
                    fn() => $this->wrap($values),
                    $this->shrink(true, $this->wrap($values)),
                );
            }
        }
    }

    /**
     * @return list<Value>
     */
    private function generate(int $size): array
    {
        return \iterator_to_array($this->set->take($size)->values());
    }

    /**
     * @param list<Value> $values
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

    private function shrink(bool $mutable, array $sequence): ?Dichotomy
    {
        if (\count($sequence) === 0) {
            return null;
        }

        return new Dichotomy(
            $this->removeHalfTheStructure($mutable, $sequence),
            $this->removeTailElement($mutable, $sequence),
        );
    }

    private function removeHalfTheStructure(bool $mutable, array $sequence): callable
    {
        // we round half down otherwise a sequence of 1 element would be shrunk
        // to a sequence of 1 element resulting in a infinite recursion
        $numberToKeep = (int) \round(\count($sequence) / 2, 0, \PHP_ROUND_HALF_DOWN);
        $shrinked = \array_slice($sequence, 0, $numberToKeep);

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $shrinked,
                $this->shrink(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $shrinked,
            $this->shrink(false, $shrinked),
        );
    }

    private function removeTailElement(bool $mutable, array $sequence): callable
    {
        $shrinked = $sequence;
        \array_pop($shrinked);

        if ($mutable) {
            return fn(): Value => Value::mutable(
                fn() => $shrinked,
                $this->shrink(true, $shrinked),
            );
        }

        return fn(): Value => Value::immutable(
            $shrinked,
            $this->shrink(false, $shrinked),
        );
    }
}
