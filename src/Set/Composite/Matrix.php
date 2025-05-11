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
                foreach ($b($rand, static fn() => true, 100) as $value) {
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
        do {
            $strategy = match ($rand->between(0, 2)) {
                0 => $this->favorTail($rand),
                1 => $this->favorHead($rand),
                2 => $this->favorRandom($rand),
            };

            foreach ($strategy as $value) {
                yield $value;

                // 50% chance to switch strategy for the next value to yield
                if ($rand->between(0, 1) === 0) {
                    break;
                }
            }
        } while (true);
    }

    /**
     * @return \Generator<Combination>
     */
    private function favorTail(Random $rand): \Generator
    {
        foreach (($this->a)($rand, static fn() => true, 100) as $a) {
            foreach (($this->combinations)($rand, static fn() => true, 100) as $combination) {
                yield $combination->unwrap()->add($a);
            }
        }
    }

    /**
     * @return \Generator<Combination>
     */
    private function favorHead(Random $rand): \Generator
    {
        foreach (($this->combinations)($rand, static fn() => true, 100) as $combination) {
            foreach (($this->a)($rand, static fn() => true, 100) as $a) {
                yield $combination->unwrap()->add($a);
            }
        }
    }

    /**
     * @return \Generator<Combination>
     */
    private function favorRandom(Random $rand): \Generator
    {
        foreach (($this->a)($rand, static fn() => true, 100) as $a) {
            foreach (($this->combinations)($rand, static fn() => true, 100) as $combination) {
                yield $combination->unwrap()->add($a);
                break;
            }
        }
    }
}
