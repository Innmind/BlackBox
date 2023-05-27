<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * This set can only contain immutable values as they're generated outside of the
 * class, so it can't be re-generated on the fly
 *
 * @template T
 * @template U
 * @implements Set<T|U>
 */
final class Elements implements Set
{
    private int $size;
    /** @var list<T|U> */
    private array $elements;
    /** @var \Closure(T|U): bool */
    private \Closure $predicate;

    /**
     * @no-named-arguments
     *
     * @param T $first
     * @param U $elements
     */
    private function __construct($first, ...$elements)
    {
        $this->size = 100;
        $this->elements = [$first, ...$elements];
        $this->predicate = static fn(): bool => true;
    }

    /**
     * @no-named-arguments
     *
     * @template A
     * @template B
     *
     * @param A $first
     * @param B $elements
     *
     * @return self<A, B>
     */
    public static function of($first, ...$elements): self
    {
        return new self($first, ...$elements);
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
        $self->predicate = static function(mixed $value) use ($previous, $predicate): bool {
            /** @var T|U $value */
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        $iterations = 0;
        $elements = \array_values(\array_filter(
            $this->elements,
            $this->predicate,
        ));

        if (\count($elements) === 0) {
            throw new EmptySet;
        }

        $max = \count($elements) - 1;

        while ($iterations < $this->size) {
            $index = $random->between(0, $max);
            /** @var mixed */
            $value = $elements[$index];

            yield Value::immutable($value);
            ++$iterations;
        }
    }
}
