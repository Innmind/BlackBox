<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Chars implements Set
{
    private $name;
    private $size;
    private $predicate;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(string $name): self
    {
        return new self($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;

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

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        $values = \range(0, 255);
        \shuffle($values);
        $values = array_map(static function(int $i): string {
            return chr($i);
        }, $values);

        $values = array_filter($values, $this->predicate);
        $values = \array_slice($values, 0, $this->size);

        return \array_reduce($values, $reducer, $carry);
    }
}
