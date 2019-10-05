<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set;

final class Matrix
{
    private $a;
    private $b;

    /**
     * @param Set<mixed> $a
     * @param Set<Combination> $b
     */
    public function __construct(Set $a, Set $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public static function of(Set $a, Set $b): self
    {
        return new self(
            $a,
            Set\FromGenerator::of(function() use ($b) {
                foreach ($b->values() as $value) {
                    yield new Combination($value);
                }
            })
        );
    }

    public function dot(Set $set): self
    {
        return new self(
            $set,
            Set\FromGenerator::of(function() {
                yield from $this->values();
            })
        );
    }

    public function values(): \Generator
    {
        foreach ($this->a->values() as $a) {
            foreach ($this->b->values() as $b) {
                yield $b->add($a);
            }
        }
    }
}
