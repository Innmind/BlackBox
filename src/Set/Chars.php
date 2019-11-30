<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @implements Set<string>
 */
final class Chars implements Set
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
        $self = clone $this;
        /**
         * @psalm-suppress MissingClosureParamType
         */
        $self->predicate = function($value) use ($predicate): bool {
            if (!($this->predicate)($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    public function values(): \Generator
    {
        $values = \range(0, 255);
        \shuffle($values);
        $values = array_map(static function(int $i): string {
            return chr($i);
        }, $values);

        $values = array_filter($values, $this->predicate);
        $values = \array_slice($values, 0, $this->size);

        yield from $values;
    }
}
