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
 * @implements Implementation<list<I>>
 */
final class Sequence implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param Implementation<I> $set
     * @param \Closure(list<I>): bool $predicate
     * @param int<1, max> $size
     */
    private function __construct(
        private Implementation $set,
        private Integers $sizes,
        private \Closure $predicate,
        private int $size,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @template U
     *
     * @param Implementation<U> $set
     *
     * @return self<U>
     */
    public static function implementation(
        Implementation $set,
        Integers $sizes,
    ): self {
        return new self(
            $set,
            $sizes,
            static fn(array $sequence): bool => \count($sequence) >= $sizes->min(),
            100,
        );
    }

    /**
     * @deprecated Use Set::sequence() instead
     * @psalm-pure
     *
     * @template U
     *
     * @param Set<U>|Provider<U> $set
     *
     * @return Provider\Sequence<U>
     */
    public static function of(Set|Provider $set): Provider\Sequence
    {
        return Set::sequence($set);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->set,
            $this->sizes->take($size),
            $this->predicate,
            $size,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): self
    {
        $previous = $this->predicate;

        return new self(
            $this->set,
            $this->sizes,
            static fn(array $value) => /** @var list<I> $value */ $previous($value) && $predicate($value),
            $this->size,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $immutable = $this
            ->set
            ->values($random, static fn() => true)
            ->current()
            ?->isImmutable() ?? false;
        $yielded = 0;

        do {
            foreach ($this->sizes->values($random, static fn() => true) as $size) {
                if ($yielded === $this->size) {
                    return;
                }

                /** @psalm-suppress ArgumentTypeCoercion */
                $values = $this->generate($size->unwrap(), $random);
                $value = match ($immutable) {
                    true => Value::immutable($values),
                    false => Value::mutable(static fn() => $values),
                };
                $value = $value->predicatedOn($this->predicate);
                $yieldable = $value->map(Sequence\Detonate::of(...));

                if (!$yieldable->acceptable()) {
                    continue;
                }

                yield $yieldable->shrinkWith(Sequence\RecursiveHalf::of($value));

                ++$yielded;
            }
        } while ($yielded < $this->size);
    }

    /**
     * @param 0|positive-int $size
     *
     * @return list<Value<I>>
     */
    private function generate(int $size, Random $rand): array
    {
        if ($size === 0) {
            return [];
        }

        return \array_values(\iterator_to_array($this->set->take($size)->values(
            $rand,
            static fn() => true,
        )));
    }
}
