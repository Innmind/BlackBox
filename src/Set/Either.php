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
 * @template T
 * @template U
 * @template V
 * @implements Implementation<T|U|V>
 */
final class Either implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param Implementation<T> $first
     * @param Implementation<U> $second
     * @param list<Implementation<V>> $rest
     * @param int<1, max> $size
     */
    private function __construct(
        private Implementation $first,
        private Implementation $second,
        private array $rest,
        private int $size,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @template A
     * @template B
     * @template C
     *
     * @param Implementation<A> $first
     * @param Implementation<B> $second
     * @param Implementation<C> $rest
     *
     * @return self<A, B, C>
     */
    public static function implementation(
        Implementation $first,
        Implementation $second,
        Implementation ...$rest,
    ): self {
        return new self($first, $second, $rest, 100);
    }

    /**
     * @deprecated Use Set::either() instead
     * @psalm-pure
     *
     * @no-named-arguments
     *
     * @template A
     * @template B
     * @template C
     *
     * @param Set<A>|Provider<A> $first
     * @param Set<B>|Provider<B> $second
     * @param Set<C>|Provider<C> $rest
     *
     * @return Set<A|B|C>
     */
    public static function any(
        Set|Provider $first,
        Set|Provider $second,
        Set|Provider ...$rest,
    ): Set {
        return Set::either($first, $second, ...$rest);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->first->take($size),
            $this->second->take($size),
            \array_map(
                static fn(Implementation $set): Implementation => $set->take($size),
                $this->rest,
            ),
            $size,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $iterations = 0;
        /** @var list<Implementation<T>|Implementation<U>|Implementation<V>> */
        $sets = [$this->first, $this->second, ...$this->rest];

        while ($iterations < $this->size) {
            $count = \count($sets);

            if ($count === 0 && $iterations === 0) {
                throw new EmptySet;
            }

            if ($count === 0) {
                return;
            }

            $setToChoose = $random->between(0, $count - 1);

            try {
                $value = $sets[$setToChoose]->values($random, $predicate)->current();

                if (\is_null($value)) {
                    continue;
                }

                yield $value;
            } catch (EmptySet $e) {
                unset($sets[$setToChoose]);
                $sets = \array_values($sets);

                continue;
            }

            ++$iterations;
        }
    }
}
