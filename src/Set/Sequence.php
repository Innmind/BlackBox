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
     * @param int<1, max> $size
     * @param int<1, max> $min
     */
    private function __construct(
        private Implementation $set,
        private Integers $sizes,
        private int $size,
        private int $min,
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
        /** @psalm-suppress ArgumentTypeCoercion */
        return new self(
            $set,
            $sizes,
            100,
            $sizes->min(),
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
            $size,
            $this->min,
        );
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $shrinker = new Sequence\Shrinker;
        $detonate = new Sequence\Detonate;
        $min = $this->min;
        $bounds = static fn(array $sequence): bool => \count($sequence) >= $min;
        $predicate = static fn(array $sequence): bool => /** @var list<I> $sequence */ $bounds($sequence) && $predicate($sequence);
        $immutable = $this
            ->set
            ->values($random, static fn() => true)
            ->current()
            ?->immutable() ?? false;
        $yielded = 0;

        do {
            foreach ($this->sizes->values($random, static fn() => true) as $size) {
                if ($yielded === $this->size) {
                    return;
                }

                /** @psalm-suppress ArgumentTypeCoercion */
                $values = $this->generate($size->unwrap(), $random);
                $value = Value::of($values)
                    ->mutable(!$immutable)
                    ->predicatedOn($predicate);
                $yieldable = $value->map($detonate);

                if (!$yieldable->acceptable()) {
                    continue;
                }

                yield $yieldable->shrinkWith($shrinker);

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
