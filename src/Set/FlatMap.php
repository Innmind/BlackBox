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
     * @param \Closure(Implementation<I>): Set<I> $wrap
     */
    private function __construct(
        private \Closure $decorate,
        private Implementation $set,
        private \Closure $wrap,
    ) {
    }

    #[\Override]
    public function __invoke(Random $random, \Closure $predicate): \Generator
    {
        $yielded = false;

        // By default we favor reusing the same seed to generate multiple values
        // from the underlying set. To generate a more wide range of seeds one
        // can use the ->randomize() method.
        foreach (($this->set)($random, static fn() => true) as $seed) {
            $set = ($this->decorate)(Seed::of(
                $this->wrap,
                $seed,
            ));

            foreach ($set($random, $predicate) as $value) {
                yield $value;
                $yielded = true;
            }

            // This means the underlying Set cannot produce any value
            if (!$yielded) {
                return;
            }
        }
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
     * @param \Closure(Implementation<V>): Set<V> $wrap
     *
     * @return self<T,V>
     */
    public static function implementation(
        callable $decorate,
        Implementation $set,
        \Closure $wrap,
    ): self {
        return new self(\Closure::fromCallable($decorate), $set, $wrap);
    }
}
