<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Composite\Vector,
    Set\Composite\Matrix,
};
use Innmind\Immutable\Sequence;

/**
 * {@inheritdoc}
 */
final class Composite implements Set
{
    private $aggregate;
    private $sets;
    private $size;
    private $predicate;

    public function __construct(
        callable $aggregate,
        Set $first,
        Set $second,
        Set ...$sets
    ) {
        $this->aggregate = $aggregate;
        $this->sets = Sequence::of($first, $second, ...$sets)->reverse();
        $this->size = null; // by default allow all combinations
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(
        callable $aggregate,
        Set ...$sets
    ): self {
        return new self($aggregate, ...$sets);
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
     * {@inheritdoc}
     */
    public function values(): \Generator
    {
        $matrix = $this
            ->sets
            ->drop(2)
            ->reduce(
                Matrix::of(
                    $this->sets->get(1),
                    $this->sets->first()
                ),
                static function(Matrix $matrix, Set $set): Matrix {
                    return $matrix->dot($set);
                }
            )
            ->values();
        $iterations = 0;

        while ($matrix->valid() && $this->continue($iterations)) {
            $value = ($this->aggregate)(...$matrix->current()->toArray());

            if (($this->predicate)($value)) {
                yield $value;
                ++$iterations;
            }

            $matrix->next();
        }
    }

    private function continue(int $iterations): bool
    {
        if (\is_null($this->size)) {
            return true;
        }

        return $iterations < $this->size;
    }
}
