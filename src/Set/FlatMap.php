<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @internal
 * @template D
 * @template I
 * @implements Implementation<D>
 */
final class FlatMap implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(Seed<I>): Implementation<D> $decorate
     * @param Implementation<I> $set
     */
    private function __construct(
        private \Closure $decorate,
        private Implementation $set,
        private int $size,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template T
     * @template V
     *
     * @param callable(Seed<V>): Implementation<T> $decorate It must be a pure function (no randomness, no side effects)
     * @param Implementation<V> $set
     *
     * @return self<T,V>
     */
    public static function implementation(
        callable $decorate,
        Implementation $set,
    ): self {
        return new self(\Closure::fromCallable($decorate), $set, 100);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        $decorate = $this->decorate;

        /** @psalm-suppress MixedArgument */
        return new self(
            static fn($value) => $decorate($value)->take($size),
            $this->set->take($size),
            $size,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate, int $size): \Generator
    {
        $iterations = 0;

        // By default we favor reusing the same seed to generate multiple values
        // from the underlying set. To generate a more wide range of seeds one
        // can use the ->randomize() method.
        foreach ($this->set->values($random, static fn() => true, $size) as $seed) {
            $set = ($this->decorate)(Seed::of($seed));

            foreach ($set->values($random, $predicate, $size) as $value) {
                yield $value;
                ++$iterations;

                if ($iterations === $this->size) {
                    return;
                }
            }
        }
    }
}
