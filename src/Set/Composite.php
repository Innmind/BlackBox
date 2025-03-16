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
 * @internal
 * @template C
 * @implements Implementation<C>
 */
final class Composite implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(mixed...): (C|Seed<C>) $aggregate
     * @param list<Implementation> $sets
     * @param ?int<1, max> $size
     */
    private function __construct(
        private \Closure $aggregate,
        private Implementation $first,
        private Implementation $second,
        private array $sets,
        private ?int $size,
        private bool $immutable,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template T
     * @no-named-arguments
     *
     * @param callable(mixed...): (T|Seed<T>) $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return self<T>
     */
    public static function implementation(
        bool $immutable,
        callable $aggregate,
        Implementation $first,
        Implementation $second,
        Implementation ...$sets,
    ): self {
        return new self(
            \Closure::fromCallable($aggregate),
            $first,
            $second,
            $sets,
            null, // by default allow all combinations
            $immutable,
        );
    }

    /**
     * @deprecated Use Set::compose()->immutable() instead
     * @psalm-pure
     *
     * @template T
     * @no-named-arguments
     *
     * @param callable(mixed...): T $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return Set<T>
     */
    public static function immutable(
        callable $aggregate,
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$sets,
    ): Set {
        return Set::compose($aggregate, $first, $second, ...$sets)
            ->immutable()
            ->toSet();
    }

    /**
     * @deprecated Use Set::compose()->mutable() instead
     * @psalm-pure
     *
     * @template T
     * @no-named-arguments
     *
     * @param callable(mixed...): T $aggregate It must be a pure function (no randomness, no side effects)
     *
     * @return Set<T>
     */
    public static function mutable(
        callable $aggregate,
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$sets,
    ): Set {
        return Set::compose($aggregate, $first, $second, ...$sets)
            ->mutable()
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return self<C>
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->aggregate,
            $this->first,
            $this->second,
            $this->sets,
            $size,
            $this->immutable,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $matrix = $this->matrix()->values($random);
        $aggregate = $this->aggregate;
        $iterations = 0;

        while ($matrix->valid() && $this->continue($iterations)) {
            /** @var Composite\Combination */
            $combination = $matrix->current();
            $immutable = $combination->immutable() && $this->immutable;
            $matrix->next();

            $value = Value::of($combination)
                ->flagMutable(!$immutable)
                ->predicatedOn($predicate);
            $mapped = $value->map(static fn($combination) => $combination->detonate($aggregate));

            if (!$mapped->acceptable()) {
                continue;
            }

            yield $mapped->shrinkWith(fn() => Composite\RecursiveNthShrink::of(
                $this->aggregate,
                $value,
            ));

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
            static fn(Matrix $matrix, Implementation $set): Matrix => $matrix->dot($set),
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
