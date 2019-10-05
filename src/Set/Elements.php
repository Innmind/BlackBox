<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Immutable\Sequence;

/**
 * {@inheritdoc}
 */
final class Elements implements Set
{
    private $size;
    private $elements;
    private $predicate;

    public function __construct($first, ...$elements)
    {
        $this->size = 100;
        $this->elements = Sequence::of($first, ...$elements);
        $this->predicate = static function(): bool {
            return true;
        };
    }

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

    /**
     * {@inheritdoc}
     */
    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->predicate = function($value) use ($predicate): bool {
            if (!($this->predicate)($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function values(): \Generator
    {
        $values = $this
            ->elements
            ->take($this->size)
            ->filter($this->predicate)
            ->toPrimitive();
        \shuffle($values);

        yield from $values;
    }
}
