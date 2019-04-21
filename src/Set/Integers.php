<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Integers implements Set
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
        $self->predicate = $predicate;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        $iterations = 0;

        do {
            $value = \random_int(\PHP_INT_MIN, \PHP_INT_MAX);

            if (!($this->predicate)($value)) {
                continue;
            }

            $carry = $reducer($carry, $value);
            ++$iterations;
        } while ($iterations < $this->size);

        return $carry;
    }
}
