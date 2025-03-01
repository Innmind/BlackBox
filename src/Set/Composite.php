<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Composite\Matrix,
    Random,
    Exception\EmptySet,
};

/**
 * @template C
 * @implements Implementation<C>
 */
final class Composite implements Implementation
{
    /** @var \Closure(mixed...): C */
    private \Closure $aggregate;
    private Set $first;
    private Set $second;
    /** @var list<Set> */
    private array $sets;
    private ?int $size;
    /** @var \Closure(C): bool */
    private \Closure $predicate;
    private bool $immutable;

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    private function __construct(
        bool $immutable,
        callable $aggregate,
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$sets,
    ) {
        $this->immutable = $immutable;
        /** @var \Closure(mixed...): C */
        $this->aggregate = \Closure::fromCallable($aggregate);
        $this->size = null; // by default allow all combinations
        $this->predicate = static fn(): bool => true;
        $this->first = Collapse::of($first);
        $this->second = Collapse::of($second);
        $this->sets = \array_map(Collapse::of(...), $sets);
    }

    /**
     * @psalm-pure
     *
     * @template T
     * @no-named-arguments
     *
     * @param callable(mixed...): T $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return self<T>
     */
    public static function immutable(
        callable $aggregate,
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$sets,
    ): self {
        return new self(true, $aggregate, $first, $second, ...$sets);
    }

    /**
     * @psalm-pure
     *
     * @template T
     * @no-named-arguments
     *
     * @param callable(mixed...): T $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return self<T>
     */
    public static function mutable(
        callable $aggregate,
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$sets,
    ): self {
        return new self(false, $aggregate, $first, $second, ...$sets);
    }

    /**
     * @psalm-mutation-free
     *
     * @return self<C>
     */
    #[\Override]
    public function take(int $size): self
    {
        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(C): bool $predicate
     *
     * @return self<C>
     */
    #[\Override]
    public function filter(callable $predicate): self
    {
        $previous = $this->predicate;
        $self = clone $this;
        $self->predicate = static function(mixed $value) use ($previous, $predicate): bool {
            /** @var C */
            $value = $value;

            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Implementation
    {
        return Decorate::immutable($map, $this);
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $matrix = $this->matrix()->values($random);
        $iterations = 0;

        while ($matrix->valid() && $this->continue($iterations)) {
            /** @var Composite\Combination */
            $combination = $matrix->current();
            $value = $combination->detonate($this->aggregate);
            $matrix->next();

            if (!($this->predicate)($value)) {
                continue;
            }

            if ($combination->immutable() && $this->immutable) {
                yield Value::immutable(
                    $value,
                    Composite\RecursiveNthShrink::of(
                        false,
                        $this->predicate,
                        $this->aggregate,
                        $combination,
                    ),
                );
            } else {
                // we don't need to re-apply the predicate when we handle mutable
                // data as the underlying data is already validated and the mutable
                // nature is about the enclosing of the data and should not be part
                // of the filtering process
                yield Value::mutable(
                    fn() => $combination->detonate($this->aggregate),
                    Composite\RecursiveNthShrink::of(
                        true,
                        $this->predicate,
                        $this->aggregate,
                        $combination,
                    ),
                );
            }

            ++$iterations;
        }

        if ($iterations === 0) {
            throw new EmptySet;
        }
    }

    private function matrix(): Matrix
    {
        $sets = [$this->first, $this->second, ...$this->sets];
        $sets = \array_reverse($sets);
        $first = \array_shift($sets);
        $second = \array_shift($sets);

        /** @psalm-suppress PossiblyNullArgument */
        return \array_reduce(
            $sets,
            static fn(Matrix $matrix, Set $set): Matrix => $matrix->dot($set),
            Matrix::of($second, $first),
        );
    }

    private function continue(int $iterations): bool
    {
        if (\is_null($this->size)) {
            return true;
        }

        return $iterations < $this->size;
    }
}
