<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @template I
 * @implements Implementation<I>
 */
final class Filter implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param Implementation<I> $set
     * @param \Closure(I): bool $predicate
     */
    private function __construct(
        private Implementation $set,
        private \Closure $predicate,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
    ): \Generator {
        $own = $this->predicate;

        $values = ($this->set)(
            $random,
            static fn($value) => /** @var I $value */ $own($value) && $predicate($value),
        );

        foreach ($values as $value) {
            if ($value->acceptable()) {
                yield $value;
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
     * @param callable(T): bool $predicate
     *
     * @return self<T>
     */
    public static function implementation(
        Implementation $set,
        callable $predicate,
    ): self {
        if ($set instanceof self) {
            /** @psalm-suppress ImpurePropertyFetch */
            $previous = $set->predicate;

            /** @psalm-suppress ImpurePropertyFetch */
            return new self(
                $set->set,
                static fn($value) => /** @var T $value */ $previous($value) && $predicate($value),
            );
        }

        return new self(
            $set,
            \Closure::fromCallable($predicate),
        );
    }
}
