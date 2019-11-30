<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<int>
 */
final class Integers implements Set
{
    private int $lowerBound;
    private int $upperBound;
    private int $size;
    private \Closure $predicate;

    private function __construct(int $lowerBound = null, int $upperBound = null)
    {
        $this->lowerBound = $lowerBound ?? \PHP_INT_MIN;
        $this->upperBound = $upperBound ?? \PHP_INT_MAX;
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function any(): self
    {
        return new self;
    }

    public static function between(int $lowerBound, int $upperBound): self
    {
        return new self($lowerBound, $upperBound);
    }

    public static function above(int $lowerBound): self
    {
        return new self($lowerBound);
    }

    public static function below(int $upperBound): self
    {
        return new self(null, $upperBound);
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
        $iterations = 0;

        do {
            $value = \random_int($this->lowerBound, $this->upperBound);

            if (!($this->predicate)($value)) {
                continue;
            }

            yield $value;
            ++$iterations;
        } while ($iterations < $this->size);
    }
}
