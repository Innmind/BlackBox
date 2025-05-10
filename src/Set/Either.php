<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
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
     */
    private function __construct(
        private Implementation $first,
        private Implementation $second,
        private array $rest,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator {
        /** @var list<Implementation<T>|Implementation<U>|Implementation<V>> */
        $sets = [$this->first, $this->second, ...$this->rest];

        while (true) {
            $count = \count($sets);

            if ($count === 0) {
                return;
            }

            $setToChoose = $random->between(0, $count - 1);

            $value = $sets[$setToChoose]($random, $predicate, $size)
                ->current();

            if (\is_null($value)) {
                unset($sets[$setToChoose]);
                $sets = \array_values($sets);

                continue;
            }

            yield $value;
        }
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
        return new self($first, $second, $rest);
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
}
