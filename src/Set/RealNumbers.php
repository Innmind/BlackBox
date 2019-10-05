<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * {@inheritdoc}
 */
final class RealNumbers implements Set
{
    private $size;
    private $predicate;

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
     * @return \Generator<int>
     */
    public function values(): \Generator
    {
        $iterations = 0;

        do {
            $value = \random_int(\PHP_INT_MIN, \PHP_INT_MAX) * \lcg_value();

            if (!($this->predicate)($value)) {
                continue;
            }

            yield $value;
            ++$iterations;
        } while ($iterations < $this->size);
    }
}
