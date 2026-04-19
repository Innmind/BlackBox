<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
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
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
    ): \Generator {
        $shrinker = Composite\Shrinker::new();
        $matrix = $this->matrix()->values($random);
        $aggregate = $this->aggregate;

        foreach ($matrix as $combination) {
            yield Value::of($combination)
                ->predicatedOn($predicate)
                ->map(static fn($combination) => $combination->detonate($aggregate))
                ->shrinkWith($shrinker);
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
        );
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
}
