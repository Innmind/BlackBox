<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
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
     *
     * @return self<T>
     */
    public static function implementation(Implementation $set): self
    {
        return new self($set, 100);
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

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->set,
            $size,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): self
    {
        return new self(
            $this->set->filter($predicate),
            $this->size,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Implementation
    {
        return Map::implementation($map, $this, true);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function flatMap(callable $map, callable $extract): Implementation
    {
        /**
         * @psalm-suppress MixedArgumentTypeCoercion
         * @psalm-suppress InvalidArgument
         */
        return FlatMap::implementation(
            static fn($input) => $extract($map($input)),
            $this,
        );
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            try {
                $value = $this->set->values($random)->current();
            } catch (EmptySet $e) {
                continue;
            }

            if (\is_null($value)) {
                continue;
            }

            yield $value;
            ++$iterations;
        }
    }
}
