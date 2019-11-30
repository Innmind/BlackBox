<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set;

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
        $combinations = Set\FromGenerator::of(static function() use ($b): \Generator {
            /** @var mixed */
            foreach ($b->values() as $value) {
                yield new Combination($value);
            }
        });

        return new self($a, $combinations);
    }

    public function dot(Set $set): self
    {
        /** @var Set<Combination> */
        $combinations = Set\FromGenerator::of(function(): \Generator {
            yield from $this->values();
        });

        return new self($set, $combinations);
    }

    /**
     * @return \Generator<Combination>
     */
    public function values(): \Generator
    {
        /** @var mixed */
        foreach ($this->a->values() as $a) {
            foreach ($this->combinations->values() as $combination) {
                yield $combination->add($a);
            }
        }
    }
}
