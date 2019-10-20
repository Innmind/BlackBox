<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Immutable\Sequence;

/**
 * {@inheritdoc}
 */
final class Either implements Set
{
    private $sets;
    private $size;
    private $predicate;

    public function __construct(Set $first, Set $second, Set ...$rest)
    {
        $this->sets = Sequence::of($first, $second, ...$rest);
        $this->size = 100;
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->sets = $this->sets->map(static function(Set $set) use ($size): Set {
            return $set->take($size);
        });
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
     * @return \Generator<mixed>
     */
    public function values(): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            $setToChoose = \random_int(0, $this->sets->size() - 1);
            $value = $this->sets->get($setToChoose)->values()->current();

            if (($this->predicate)($value)) {
                yield $value;
                ++$iterations;
            }
        }
    }
}
