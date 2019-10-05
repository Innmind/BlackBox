<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class Strings implements Set
{
    private $maxLength;
    private $size;
    private $predicate;
    private $values;

    public function __construct(int $maxLength = 128)
    {
        $this->maxLength = $maxLength;
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(int $maxLength = 128): self
    {
        return new self($maxLength);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;
        $self->values = null;

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
        $self->values = null;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        if (\is_null($this->values)) {
            $this->values = \iterator_to_array($this->values());
        }

        return \array_reduce($this->values, $reducer, $carry);
    }

    /**
     * @return \Generator<string>
     */
    public function values(): \Generator
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

            yield $value;
            ++$iterations;
        } while ($iterations < $this->size);
    }
}
