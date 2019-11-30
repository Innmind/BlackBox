<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Json\Json;

/**
 * {@inheritdoc}
 */
final class UnsafeStrings implements Set
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
     * @return \Generator<string>
     */
    public function values(): \Generator
    {
        $values = Json::decode(\file_get_contents(__DIR__.'/unsafeStrings.json'));
        \shuffle($values);

        $values = array_filter($values, $this->predicate);
        $values = \array_slice($values, 0, $this->size);

        yield from $values;
    }
}
