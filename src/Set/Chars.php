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
        $this->predicate = static fn(): bool => true;
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
        $self->predicate = static function(string $value) use ($previous, $predicate): bool {
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
        $values = \range(0, 255);
        \shuffle($values);
        $values = \array_map(
            static fn(int $i): string => \chr($i),
            $values,
        );

        $values = \array_filter($values, $this->predicate);
        $values = \array_slice($values, 0, $this->size);

        foreach ($values as $value) {
            yield Value::immutable($value);
        }
    }
}
