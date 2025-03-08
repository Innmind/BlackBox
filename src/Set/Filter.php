<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Random;

/**
 * @internal
 * @template T
 * @implements Implementation<T>
 */
final class Filter implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(T): bool $predicate
     * @param Implementation<T> $set
     */
    private function __construct(
        private \Closure $predicate,
        private Implementation $set,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template A
     *
     * @param callable(A): bool $predicate
     * @param Implementation<A> $set
     *
     * @return self<A>
     */
    public static function implementation(
        callable $predicate,
        Implementation $set,
    ): self {
        return new self(\Closure::fromCallable($predicate), $set);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->predicate,
            $this->set->take($size),
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): self
    {
        /** @psalm-suppress MixedArgument */
        return new self(
            $this->predicate,
            $this->set->filter(static fn(mixed $value): bool => $predicate($value)),
        );
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        foreach ($this->set->values($random) as $value) {
            $value = $value->predicatedOn($this->predicate);

            if (!$value->acceptable()) {
                continue;
            }

            yield $value;
        }
    }
}
