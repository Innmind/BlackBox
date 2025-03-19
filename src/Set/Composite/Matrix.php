<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\{
    Set\Implementation,
    Set\FromGenerator,
    Random,
};

/**
 * @internal
 */
final class Matrix
{
    private Implementation $a;
    /** @var Implementation<Combination> */
    private Implementation $combinations;

    /**
     * @param Implementation<mixed> $a
     * @param Implementation<Combination> $combinations
     */
    public function __construct(Implementation $a, Implementation $combinations)
    {
        $this->a = $a;
        $this->combinations = $combinations;
    }

    /**
     * @internal
     */
    public static function of(Implementation $a, Implementation $b): self
    {
        /** @var Implementation<Combination> */
        $combinations = FromGenerator::implementation(
            static function(Random $rand) use ($b): \Generator {
                foreach ($b->values($rand, static fn() => true) as $value) {
                    yield Combination::startWith($value);
                }
            },
            immutable: true,
        );

        return new self($a, $combinations);
    }

    public function dot(Implementation $set): self
    {
        /** @var Implementation<Combination> */
        $combinations = FromGenerator::implementation(
            function(Random $rand): \Generator {
                yield from $this->values($rand);
            },
            immutable: true,
        );

        return new self($set, $combinations);
    }

    /**
     * @return \Generator<Combination>
     */
    public function values(Random $rand): \Generator
    {
        foreach ($this->a->values($rand, static fn() => true) as $a) {
            foreach ($this->combinations->values($rand, static fn() => true) as $combination) {
                yield $combination->unwrap()->add($a);
            }
        }
    }
}
