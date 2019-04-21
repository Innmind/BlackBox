<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class RealNumbers implements Set
{
    private $name;
    private $size;
    private $predicates = [];

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->size = 100;
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
        $self->predicates[] = $predicate;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        $iterations = 0;

        do {
            $value = \random_int(\PHP_INT_MIN, \PHP_INT_MAX) * \lcg_value();

            foreach ($this->predicates as $predicate) {
                if (!$predicate($value)) {
                    continue 2;
                }
            }

            $carry = $reducer($carry, $value);
            ++$iterations;
        } while ($iterations < $this->size);

        return $carry;
    }
}
