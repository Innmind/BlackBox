<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<float>
 */
final class RealNumbers implements Set
{
    private int $size;
    private \Closure $predicate;

    public function __construct()
    {
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function any(): self
    {
        return new self;
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

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(): \Generator
    {
        $iterations = 0;

        do {
            $value = \random_int(\PHP_INT_MIN, \PHP_INT_MAX) * \lcg_value();

            if (!($this->predicate)($value)) {
                continue;
            }

            yield Value::immutable($value);
            ++$iterations;
        } while ($iterations < $this->size);
    }
}
