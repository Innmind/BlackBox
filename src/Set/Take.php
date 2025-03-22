<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @template I
 * @implements Implementation<I>
 */
final class Take implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param Implementation<I> $set
     * @param int<1, max> $size
     */
    private function __construct(
        private Implementation $set,
        private int $size,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template T
     *
     * @param Implementation<T> $set
     * @param int<1, max> $size
     *
     * @return self<T>
     */
    public static function implementation(
        Implementation $set,
        int $size,
    ): self {
        if ($set instanceof self) {
            /** @psalm-suppress ImpurePropertyFetch */
            if ($set->size < $size) {
                return $set;
            }

            /** @psalm-suppress ImpurePropertyFetch */
            $set = $set->set;
        }

        return new self($set, $size);
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator {
        yield from ($this->set)(
            $random,
            $predicate,
            $this->size,
        );
    }
}
