<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\{
    Set,
    Random,
};

final class Matrix
{
    private Set $a;
    /** @var Set<Combination> */
    private Set $combinations;

    /**
     * @param Set<mixed> $a
     * @param Set<Combination> $combinations
     */
    public function __construct(Set $a, Set $combinations)
    {
        $this->a = $a;
        $this->combinations = $combinations;
    }

    public static function of(Set $a, Set $b): self
    {
        /** @var Set<Combination> */
        $combinations = Set\FromGenerator::of(static function(Random $rand) use ($b): \Generator {
            foreach ($b->values($rand) as $value) {
                yield new Combination($value);
            }
        });

        return new self($a, $combinations);
    }

    public function dot(Set $set): self
    {
        /** @var Set<Combination> */
        $combinations = Set\FromGenerator::of(function(Random $rand): \Generator {
            yield from $this->values($rand);
        });

        return new self($set, $combinations);
    }

    /**
     * @return \Generator<Combination>
     */
    public function values(Random $rand): \Generator
    {
        foreach ($this->a->values($rand) as $a) {
            foreach ($this->combinations->values($rand) as $combination) {
                yield $combination->unwrap()->add($a);
            }
        }
    }
}
