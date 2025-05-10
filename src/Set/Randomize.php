<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @internal
 * @template I
 * @implements Implementation<I>
 */
final class Randomize implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param Implementation<I> $set
     */
    private function __construct(
        private Implementation $set,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator {
        $iterations = 0;

        while ($iterations < $size) {
            $value = ($this->set)($random, $predicate, $size)->current();

            if (\is_null($value)) {
                continue;
            }

            yield $value;
            ++$iterations;
        }
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template T
     *
     * @param Implementation<T> $set
     *
     * @return self<T>
     */
    public static function implementation(Implementation $set): self
    {
        return new self($set);
    }

    /**
     * @deprecated Use $set->randomize() instead
     * @psalm-pure
     *
     * @template T
     *
     * @param Set<T>|Provider<T> $set
     *
     * @return Set<T>
     */
    public static function of(Set|Provider $set): Set
    {
        return Collapse::of($set)->randomize();
    }
}
