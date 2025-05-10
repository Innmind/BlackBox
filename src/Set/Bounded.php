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
        int $size,
    ): \Generator {
        $values = ($this->set)(
            $random,
            $predicate,
            $size,
        );

        $iterations = 0;

        foreach ($values as $value) {
            yield $value;
            ++$iterations;

            if ($iterations === 100) {
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
