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
 * @implements Set<mixed>
 *
 */
final class Elements implements Set
{
    private int $size;
    private array $elements;
    /** @var \Closure(mixed): bool */
    private \Closure $predicate;

    /**
     * @no-named-arguments
     *
     * @param mixed $first
     * @param mixed $elements
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
     * @param mixed $first
     * @param mixed $elements
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
        /** @psalm-suppress MissingClosureParamType */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
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
