<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};

/**
 * @internal
 * This set can only contain immutable values as they're generated outside of the
 * class, so it can't be re-generated on the fly
 *
 * @template T
 * @template U
 * @implements Implementation<T|U>
 */
final class Elements implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     * @param T $first
     * @param list<U> $elements
     */
    private function __construct(
        private mixed $first,
        private array $elements,
        private int $size,
    ) {
    }

    /**
     * @internal
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
    public static function implementation($first, ...$elements): self
    {
        return new self($first, $elements, 100);
    }

    /**
     * @deprecated Use Set::of() instead
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
     * @return Set<A|B>
     */
    public static function of($first, ...$elements): Set
    {
        return Set::of($first, ...$elements);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->first,
            $this->elements,
            $size,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $iterations = 0;
        $elements = \array_values(\array_filter(
            [$this->first, ...$this->elements],
            $predicate,
        ));

        if (\count($elements) === 0) {
            throw new EmptySet;
        }

        $max = \count($elements) - 1;

        while ($iterations < $this->size) {
            $index = $random->between(0, $max);
            /** @var mixed */
            $value = $elements[$index];

            yield Value::of($value)->predicatedOn($predicate);
            ++$iterations;
        }
    }
}
