<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Composite\Vector,
    Set\Composite\Matrix,
};
use Innmind\Immutable\Sequence;

final class Composite implements Set
{
    private $name;
    private $aggregate;
    private $sets;
    private $size;
    private $predicate;
    private $values;

    public function __construct(
        string $name,
        callable $aggregate,
        Set $first,
        Set $second,
        Set ...$sets
    ) {
        $this->name = $name;
        $this->aggregate = $aggregate;
        $this->sets = Sequence::of($first, $second, ...$sets)->reverse();
        $this->size = null; // by default allow all combinations
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(
        string $name,
        callable $aggregate,
        Set ...$sets
    ): self {
        return new self($name, $aggregate, ...$sets);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;
        $self->values = null;

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
        $self->values = null;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function reduce($carry, callable $reducer)
    {
        if (\is_null($this->values)) {
            $matrix = $this->sets->drop(2)->reduce(
                Vector::of($this->sets->get(1))->dot(Vector::of($this->sets->first())),
                static function(Matrix $matrix, Set $set): Matrix {
                    return $matrix->dot($set);
                }
            );
            $iterations = 0;
            $values = [];

            while ($matrix->valid() && $this->continue($iterations)) {
                $value = ($this->aggregate)(...$matrix->current());

                if (($this->predicate)($value)) {
                    $values[] = $value;
                    ++$iterations;
                }

                $matrix->next();
            }

            $this->values = $values;
        }

        return \array_reduce($this->values, $reducer, $carry);
    }

    private function continue(int $iterations): bool
    {
        if (\is_null($this->size)) {
            return true;
        }

        return $iterations < $this->size;
    }
}