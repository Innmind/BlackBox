<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Immutable\Sequence;

final class Elements implements Set
{
    private $name;
    private $size;
    private $elements;
    private $predicate;
    private $values;

    public function __construct(string $name, $first, ...$elements)
    {
        $this->name = $name;
        $this->size = 100;
        $this->elements = Sequence::of($first, ...$elements);
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(string $name, $first, ...$elements): self
    {
        return new self($name, $first, ...$elements);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;
        $self->values = null;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->predicate = function($value) use ($predicate): bool {
            if (!($this->predicate)($value)) {
                return false;
            }

            return $predicate($value);
        };
        $self->values = null;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        if (\is_null($this->values)) {
            $values = $this
                ->elements
                ->take($this->size)
                ->filter($this->predicate)
                ->toPrimitive();
            \shuffle($values);

            $this->values = $values;
        }

        return \array_reduce($this->values, $reducer, $carry);
    }
}
