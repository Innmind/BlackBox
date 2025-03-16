<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Seed,
    Value,
};

/**
 * @internal
 */
final class RecursiveNthShrink
{
    /**
     * @param callable(mixed...): (mixed|Seed<mixed>) $aggregate,
     * @param int<0, max> $n
     */
    public function __construct(
        private $aggregate,
        private int $n = 0,
    ) {
    }

    /**
     * @param Value<Combination> $value
     */
    public function __invoke(Value $value): ?Dichotomy
    {
        $mapped = $value->map(fn($combination) => $combination->detonate($this->aggregate));
        $combination = $value->unwrap();

        if (!$mapped->acceptable()) {
            return null;
        }

        return Dichotomy::of(
            $this->shrinkANth($value, $this->n),
            $this->shrinkANth($value, $this->n + 1),
        );
    }

    /**
     * @internal
     * @template A
     *
     * @param callable(mixed...): (mixed|Seed<mixed>) $aggregate
     * @param Value<Combination> $value
     * @param 0|positive-int $n
     *
     * @return ?Dichotomy<Mixed>
     */
    public static function of(
        callable $aggregate,
        Value $value,
        int $n = 0,
    ): ?Dichotomy {
        return (new self($aggregate, $n))($value);
    }

    /**
     * @param Value<Combination> $value
     * @param int<0, max> $n
     */
    private function shrinkANth(Value $value, int $n): ?Value
    {
        $combination = $value->unwrap();
        $values = $combination->values();

        if (!\array_key_exists($n, $values)) {
            return $this->shrinkBNth(
                $value,
            );
        }

        $shrunk = $combination->aShrinkNth($n);

        if (\is_null($shrunk)) {
            return $this->shrinkANth(
                $value,
                $n + 1,
            );
        }

        $shrunk = $value->map(static fn() => $shrunk);
        $mapped = $shrunk->map(
            fn($combination) => $combination->detonate($this->aggregate),
        );

        if (!$mapped->acceptable()) {
            return $this->shrinkANth(
                $value,
                $n + 1,
            );
        }

        return $mapped->shrinkWith(fn() => self::of(
            $this->aggregate,
            $shrunk,
            $n,
        ));
    }

    /**
     * @param Value<Combination> $value
     * @param int<0, max> $n
     */
    private function shrinkBNth(
        Value $value,
        int $n = 0,
    ): ?Value {
        $combination = $value->unwrap();
        $values = $combination->values();

        if (!\array_key_exists($n, $values)) {
            return null;
        }

        $shrunk = $combination->bShrinkNth($n);

        if (\is_null($shrunk)) {
            return $this->shrinkBNth(
                $value,
                $n + 1,
            );
        }

        $shrunk = $value->map(static fn() => $shrunk);
        $mapped = $shrunk->map(
            fn($combination) => $combination->detonate($this->aggregate),
        );

        if (!$mapped->acceptable()) {
            return $this->shrinkBNth(
                $value,
                $n + 1,
            );
        }

        return $mapped->shrinkWith(fn() => RecursiveNthShrink::of(
            $this->aggregate,
            $shrunk,
            $n,
        ));
    }
}
