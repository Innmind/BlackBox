<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @template I
 * @implements Implementation<I>
 */
final class Bounded implements Implementation
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
    ): \Generator {
        $values = ($this->set)(
            $random,
            $predicate,
        );

        $remaining = 100;

        foreach ($values as $value) {
            yield $value;

            if ($value->unbounded()) {
                --$remaining;
            }

            if ($remaining === 0) {
                return;
            }
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
    public static function implementation(
        Implementation $set,
    ): self {
        return new self($set);
    }
}
