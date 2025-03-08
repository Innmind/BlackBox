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
            static function(array $value) use ($previous, $predicate): bool {
                /** @var list<I> $value */
                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
            $this->size,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function flatMap(callable $map, callable $extract): Implementation
    {
        /** @psalm-suppress MixedArgument Due to $input */
        return FlatMap::implementation(
            static fn($input) => $extract($map($input)),
            $this,
        );
    }

    #[\Override]
    public function values(Random $random): \Generator
    {
        $immutable = $this->set->values($random)->current()?->isImmutable() ?? false;
        $yielded = 0;

        do {
            foreach ($this->sizes->values($random) as $size) {
                if ($yielded === $this->size) {
                    return;
                }

                /** @psalm-suppress ArgumentTypeCoercion */
                $values = $this->generate($size->unwrap(), $random);

                if (!($this->predicate)(Sequence\Detonate::of($values))) {
                    continue;
                }

                if ($immutable) {
                    yield Value::immutable(Sequence\Detonate::of($values))
                        ->shrinkWith(Sequence\RecursiveHalf::of(
                            false,
                            $this->predicate,
                            $values,
                        ));
                } else {
                    yield Value::mutable(static fn() => Sequence\Detonate::of($values))
                        ->shrinkWith(Sequence\RecursiveHalf::of(
                            true,
                            $this->predicate,
                            $values,
                        ));
                }

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

        return \array_values(\iterator_to_array($this->set->take($size)->values($rand)));
    }
}
