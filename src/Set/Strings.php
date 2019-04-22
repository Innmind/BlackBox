<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Strings implements Set
{
    private $name;
    private $maxLength;
    private $size;
    private $predicate;

    public function __construct(string $name, int $maxLength = 128)
    {
        $this->name = $name;
        $this->maxLength = $maxLength;
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(string $name, int $maxLength = 128): self
    {
        return new self($name, $maxLength);
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
        $iterations = 0;

        do {
            $value = '';

            foreach (range(1, \random_int(2, $this->maxLength)) as $_) {
                $value .= \chr(\random_int(33, 126));
            }

            if (!($this->predicate)($value)) {
                continue ;
            }

            $carry = $reducer($carry, $value);
            ++$iterations;
        } while ($iterations < $this->size);

        return $carry;
    }
}
