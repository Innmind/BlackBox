<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<mixed>
 */
final class Elements implements Set
{
    private int $size;
    private array $elements;
    private \Closure $predicate;

    /**
     * @param mixed $first
     * @param mixed $elements
     */
    public function __construct($first, ...$elements)
    {
        $this->size = 100;
        $this->elements = [$first, ...$elements];
        $this->predicate = static function(): bool {
            return true;
        };
    }

    /**
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
        /**
         * @psalm-suppress MissingClosureParamType
         */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    public function values(): \Generator
    {
        $values = \array_slice($this->elements, 0, $this->size);
        $values = \array_filter($values, $this->predicate);
        \shuffle($values);

        yield from $values;
    }
}
