<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Composite\Matrix,
    Set\Composite\Combination,
};

/**
 * @template C
 * @implements Set<C>
 */
final class Composite implements Set
{
    /** @var \Closure(mixed...): C */
    private \Closure $aggregate;
    /** @var list<Set<mixed>> */
    private array $sets;
    private ?int $size;
    private \Closure $predicate;
    private bool $immutable;

    private function __construct(
        bool $immutable,
        callable $aggregate,
        Set $first,
        Set $second,
        Set ...$sets
    ) {
        $sets = [$first, $second, ...$sets];

        $this->immutable = $immutable;
        /** @var \Closure(mixed...): C */
        $this->aggregate = \Closure::fromCallable($aggregate);
        $this->sets = \array_reverse($sets);
        $this->size = null; // by default allow all combinations
        $this->predicate = static fn(): bool => true;
    }

    /**
     * @template T
     *
     * @param callable(mixed...): T $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return self<T>
     */
    public static function immutable(
        callable $aggregate,
        Set $first,
        Set ...$sets
    ): self {
        return new self(true, $aggregate, $first, ...$sets);
    }

    /**
     * @template T
     *
     * @param callable(mixed...): T $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return self<T>
     */
    public static function mutable(
        callable $aggregate,
        Set $first,
        Set ...$sets
    ): self {
        return new self(false, $aggregate, $first, ...$sets);
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
        /** @psalm-suppress MissingClosureParamType */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            /** @var C */
            $value = $value;

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
            static fn(Matrix $matrix, Set $set): Matrix => $matrix->dot($set),
            Matrix::of($second, $first),
        );
        $matrix = $matrix->values();
        $iterations = 0;

        while ($matrix->valid() && $this->continue($iterations)) {
            $combination = $matrix->current();
            /** @var mixed */
            $value = ($this->aggregate)(...$combination->unwrap());
            $matrix->next();

            if (!($this->predicate)($value)) {
                continue;
            }

            if ($combination->immutable() && $this->immutable) {
                yield Value::immutable(
                    $value,
                    $this->shrink(false, $combination),
                );
            } else {
                // we don't need to re-apply the predicate when we handle mutable
                // data as the underlying data is already validated and the mutable
                // nature is about the enclosing of the data and should not be part
                // of the filtering process
                /** @psalm-suppress MissingClosureReturnType */
                yield Value::mutable(
                    fn() => ($this->aggregate)(...$combination->unwrap()),
                    $this->shrink(true, $combination),
                );
            }

            ++$iterations;
        }
    }

    private function continue(int $iterations): bool
    {
        if (\is_null($this->size)) {
            return true;
        }

        return $iterations < $this->size;
    }

    /**
     * @return Dichotomy<C>|null
     */
    private function shrink(bool $mutable, Combination $combination): ?Dichotomy
    {
        if (!$combination->shrinkable()) {
            return null;
        }

        $shrinked = $combination->shrink();

        return new Dichotomy(
            $this->shrinkWithStrategy($mutable, $combination, $shrinked['a']),
            $this->shrinkWithStrategy($mutable, $combination, $shrinked['b']),
        );
    }

    /**
     * @return callable(): Value<C>
     */
    private function shrinkWithStrategy(
        bool $mutable,
        Combination $combination,
        Combination $strategy
    ): callable {
        $shrinked = ($this->aggregate)(...$strategy->unwrap());

        if (!($this->predicate)($shrinked)) {
            return $this->identity($mutable, $combination);
        }

        if ($mutable) {
            /** @psalm-suppress MissingClosureReturnType */
            return fn(): Value => Value::mutable(
                fn() => ($this->aggregate)(...$strategy->unwrap()),
                $this->shrink(true, $strategy),
            );
        }

        return fn(): Value => Value::immutable(
            $shrinked,
            $this->shrink(false, $strategy),
        );
    }

    /**
     * @return callable(): Value<C>
     */
    private function identity(bool $mutable, Combination $combination): callable
    {
        if ($mutable) {
            /** @psalm-suppress MissingClosureReturnType */
            return fn(): Value => Value::mutable(
                fn() => ($this->aggregate)(...$combination->unwrap()),
            );
        }

        return fn(): Value => Value::immutable(
            ($this->aggregate)(...$combination->unwrap()),
        );
    }
}
