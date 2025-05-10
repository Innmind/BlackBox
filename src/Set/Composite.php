<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Composite\Matrix,
    Random,
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
     */
    private function __construct(
        private \Closure $aggregate,
        private Implementation $first,
        private Implementation $second,
        private array $sets,
        private bool $immutable,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator {
        $shrinker = Composite\Shrinker::new();
        $matrix = $this->matrix()->values($random);
        $aggregate = $this->aggregate;
        $iterations = 0;

        while ($matrix->valid() && $this->continue($iterations, $size)) {
            /** @var Composite\Combination */
            $combination = $matrix->current();
            $immutable = $combination->immutable() && $this->immutable;
            $matrix->next();

            $value = Value::of($combination)
                ->mutable(!$immutable)
                ->predicatedOn($predicate);
            $mapped = $value->map(static fn($combination) => $combination->detonate($aggregate));

            if (!$mapped->acceptable()) {
                continue;
            }

            yield $mapped->shrinkWith($shrinker);

            ++$iterations;
        }
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

    private function continue(int $iterations, int $size): bool
    {
        return $iterations < $size;
    }
}
