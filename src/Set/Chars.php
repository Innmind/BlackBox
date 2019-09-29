<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Chars implements Set
{
    private $size;
    private $predicate;
    private $values;

    public function __construct()
    {
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(): self
    {
        return new self;
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
            $values = \range(0, 255);
            \shuffle($values);
            $values = array_map(static function(int $i): string {
                return chr($i);
            }, $values);

            $values = array_filter($values, $this->predicate);
            $values = \array_slice($values, 0, $this->size);

            $this->values = $values;
        }

        return \array_reduce($this->values, $reducer, $carry);
    }
}
