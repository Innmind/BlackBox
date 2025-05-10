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
     * @param int<1, max> $min
     */
    private function __construct(
        private Implementation $set,
        private Integers $sizes,
        private int $min,
    ) {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
        int $size,
    ): \Generator {
        $shrinker = new Sequence\Shrinker;
        $detonate = new Sequence\Detonate;
        $min = $this->min;
        $bounds = static fn(array $sequence): bool => \count($sequence) >= $min;
        $predicate = static fn(array $sequence): bool => /** @var list<I> $sequence */ $bounds($sequence) && $predicate($sequence);
        $immutable = ($this->set)($random, static fn() => true, 1)
            ->current()
            ?->immutable() ?? false;

        do {
            foreach (($this->sizes)($random, static fn() => true, $size) as $nextSize) {
                /** @psalm-suppress ArgumentTypeCoercion */
                $values = $this->generate($nextSize->unwrap(), $random);
                $value = Value::of($values)
                    ->mutable(!$immutable)
                    ->predicatedOn($predicate);
                $yieldable = $value->map($detonate);

                if (!$yieldable->acceptable()) {
                    continue;
                }

                yield $yieldable->shrinkWith($shrinker);
            }
        } while (true);
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
     * @param 0|positive-int $size
     *
     * @return list<Value<I>>
     */
    private function generate(int $size, Random $rand): array
    {
        if ($size === 0) {
            return [];
        }

        $set = Take::implementation(
            $this->set,
            $size,
        );

        return \array_values(\iterator_to_array($set(
            $rand,
            static fn() => true,
            $size,
        )));
    }
}
