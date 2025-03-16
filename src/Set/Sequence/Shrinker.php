<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};

/**
 * @internal
 */
final class Shrinker
{
    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function recursiveHalf(Value $value): ?Dichotomy
    {
        if (\count($value->unwrap()) === 0) {
            return null;
        }

        if (!$value->map(Detonate::of(...))->acceptable()) {
            return null;
        }

        return Dichotomy::of(
            self::removeHalf($value),
            self::removeTail($value),
        );
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function recursiveHead(Value $value): ?Dichotomy
    {
        if (\count($value->unwrap()) === 0) {
            return null;
        }

        if (!$value->map(Detonate::of(...))->acceptable()) {
            return null;
        }

        return Dichotomy::of(
            self::removeHead($value),
            self::removeNth($value),
        );
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function recursiveTail(Value $value): ?Dichotomy
    {
        if (\count($value->unwrap()) === 0) {
            return null;
        }

        if (!$value->map(Detonate::of(...))->acceptable()) {
            return null;
        }

        return Dichotomy::of(
            self::removeTail($value),
            self::removeHead($value),
        );
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param positive-int $n
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function recursiveNth(Value $value, int $n = 1): ?Dichotomy
    {
        if (\count($value->unwrap()) === 0) {
            return null;
        }

        if (!$value->map(Detonate::of(...))->acceptable()) {
            return null;
        }

        return Dichotomy::of(
            self::removeNth($value, $n),
            self::removeNth($value, $n + 1),
        );
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param 0|positive-int $n
     *
     * @return ?Dichotomy<list<A>>
     */
    public static function recursiveNthShrink(Value $value, int $n = 0): ?Dichotomy
    {
        if (\count($value->unwrap()) === 0) {
            return null;
        }

        if (!$value->map(Detonate::of(...))->acceptable()) {
            return null;
        }

        return Dichotomy::of(
            self::shrinkANth($value, $n),
            self::shrinkANth($value, $n + 1),
        );
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Value<list<A>>
     */
    public static function removeHalf(Value $value): ?Value
    {
        // we round half down otherwise a sequence of 1 element would be shrunk
        // to a sequence of 1 element resulting in a infinite recursion
        $shrunk = $value->map(static function($sequence) {
            $numberToKeep = (int) \round(\count($sequence) / 2, 0, \PHP_ROUND_HALF_DOWN);

            return \array_slice($sequence, 0, $numberToKeep);
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return self::removeTail($value);
        }

        return $detonated->shrinkWith(static fn() => self::recursiveHalf($shrunk));
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Value<list<A>>
     */
    public static function removeTail(Value $value): ?Value
    {
        $shrunk = $value->map(static function($sequence) {
            $shrunk = $sequence;
            \array_pop($shrunk);

            return $shrunk;
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return self::removeHead($value);
        }

        return $detonated->shrinkWith(static fn() => self::recursiveTail($shrunk));
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Value<list<A>>
     */
    public static function removeHead(Value $value): ?Value
    {
        $shrunk = $value->map(static function($sequence) {
            $shrunk = $sequence;
            \array_shift($shrunk);

            return $shrunk;
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return self::removeNth($value);
        }

        return $detonated->shrinkWith(static fn() => self::recursiveHead($shrunk));
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param positive-int $n
     *
     * @return ?Value<list<A>>
     */
    public static function removeNth(Value $value, int $n = 1): ?Value
    {
        if (!\array_key_exists($n, $value->unwrap())) {
            return self::shrinkANth($value);
        }

        $shrunk = $value->map(static function($sequence) use ($n) {
            $shrunk = [];

            foreach ($sequence as $i => $value) {
                if ($i !== $n) {
                    $shrunk[] = $value;
                }
            }

            return $shrunk;
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return self::shrinkANth($value);
        }

        return $detonated->shrinkWith(static fn() => self::recursiveNth(
            $shrunk,
            $n,
        ));
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param 0|positive-int $n
     *
     * @return ?Value<list<A>>
     */
    public static function shrinkANth(Value $value, int $n = 0): ?Value
    {
        $sequence = $value->unwrap();

        if (!\array_key_exists($n, $sequence)) {
            return self::shrinkBNth($value);
        }

        $nShrunk = $sequence[$n]->shrink();

        if (\is_null($nShrunk)) {
            return self::shrinkANth($value, $n + 1);
        }

        $shrunk = $value->map(static function($sequence) use ($n, $nShrunk) {
            $sequence[$n] = $nShrunk->a();

            return \array_values($sequence);
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return self::shrinkANth($value, $n + 1);
        }

        return $detonated->shrinkWith(static fn() => self::recursiveNthShrink(
            $shrunk,
            $n,
        ));
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     * @param 0|positive-int $n
     *
     * @return ?Value<list<A>>
     */
    public static function shrinkBNth(Value $value, int $n = 0): ?Value
    {
        $sequence = $value->unwrap();

        if (!\array_key_exists($n, $sequence)) {
            return null;
        }

        $nShrunk = $sequence[$n]->shrink();

        if (\is_null($nShrunk)) {
            return self::shrinkBNth($value, $n + 1);
        }

        $shrunk = $value->map(static function($sequence) use ($n, $nShrunk) {
            $sequence[$n] = $nShrunk->b();

            return \array_values($sequence);
        });
        $detonated = $shrunk->map(Detonate::of(...));

        if (!$detonated->acceptable()) {
            return self::shrinkBNth($value, $n + 1);
        }

        return $detonated->shrinkWith(static fn() => self::recursiveNthShrink(
            $shrunk,
            $n,
        ));
    }
}
