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
    /** @var positive-int */
    private int $size;
    /** @var T */
    private mixed $first;
    /** @var list<U> */
    private array $elements;
    /** @var \Closure(T|U): bool */
    private \Closure $predicate;

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     * @param \Closure(T|U): bool $predicate
     * @param T $first
     * @param list<U> $elements
     */
    private function __construct(
        int $size,
        \Closure $predicate,
        mixed $first,
        array $elements,
    ) {
        $this->size = $size;
        $this->predicate = $predicate;
        $this->first = $first;
        $this->elements = $elements;
    }

    /**
     * @psalm-pure
     *
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
        return new self(100, static fn(): bool => true, $first, $elements);
    }

    /**
     * @psalm-mutation-free
     */
    public function take(int $size): Set
    {
        return new self(
            $size,
            $this->predicate,
            $this->first,
            $this->elements,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;

        return new self(
            $this->size,
            static function(mixed $value) use ($previous, $predicate): bool {
                /** @var T|U $value */
                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
            $this->first,
            $this->elements,
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
        $iterations = 0;
        $elements = \array_values(\array_filter(
            [$this->first, ...$this->elements],
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
