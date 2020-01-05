<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Composite\Matrix,
};

final class Composite implements Set
{
    private \Closure $aggregate;
    /** @var list<Set> */
    private array $sets;
    private ?int $size;
    private \Closure $predicate;

    public function __construct(
        callable $aggregate,
        Set $first,
        Set $second,
        Set ...$sets
    ) {
        $sets = [$first, $second, ...$sets];

        $this->aggregate = \Closure::fromCallable($aggregate);
        $this->sets = \array_reverse($sets);
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
        $sets = $this->sets;
        $first = \array_shift($sets);
        $second = \array_shift($sets);
        $matrix = \array_reduce(
            $sets,
            static function(Matrix $matrix, Set $set): Matrix {
                return $matrix->dot($set);
            },
            Matrix::of(
                $second,
                $first,
            ),
        );
        $matrix = $matrix->values();
        $iterations = 0;

        while ($matrix->valid() && $this->continue($iterations)) {
            /** @var mixed */
            $value = ($this->aggregate)(...$matrix->current()->toArray());

            if (($this->predicate)($value)) {
                yield Value::immutable($value);
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
