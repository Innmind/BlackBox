<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

use Innmind\BlackBox\Set\{
    Value,
    Value\End,
    Dichotomy,
};

/**
 * @internal
 * @implements Value\Shrinker<list<Value<mixed>>>
 */
final class Shrinker implements Value\Shrinker
{
    /**
     * @param int<0, max> $n
     */
    public function __construct(
        private Strategy $strategy = Strategy::recursiveHalf,
        private int $n = 0,
    ) {
    }

    #[\Override]
    public function __invoke(Value $value): ?Dichotomy
    {
        if ($value->unwrap() === []) {
            return null;
        }

        if (!$value->acceptable()) {
            return null;
        }

        return match ($this->strategy) {
            Strategy::recursiveHalf => Dichotomy::of(
                $this->removeHalf($value),
                $this->removeTail($value),
            ),
            Strategy::recursiveTail => Dichotomy::of(
                $this->removeTail($value),
                $this->removeHead($value),
            ),
            Strategy::recursiveHead => Dichotomy::of(
                $this->removeHead($value),
                $this->removeNth($value),
            ),
            Strategy::recursiveNth => Dichotomy::of(
                $this->removeNth($value, $this->n),
                $this->removeNth($value, $this->n + 1),
            ),
            Strategy::recursiveNthShrink => Dichotomy::of(
                $this->shrinkANth($value, $this->n),
                $this->shrinkANth($value, $this->n + 1),
            ),
        };
    }

    /**
     * @param Value<list<Value<mixed>>> $value
     *
     * @return Value<list<Value<mixed>>>
     */
    private function removeHalf(Value $value): ?Value
    {
        // we round half down otherwise a sequence of 1 element would be shrunk
        // to a sequence of 1 element resulting in a infinite recursion
        $shrunk = $value->shrinkVia(static function($sequence) {
            $numberToKeep = (int) \round(\count($sequence) / 2, 0, \PHP_ROUND_HALF_DOWN);

            return \array_slice($sequence, 0, $numberToKeep);
        });

        if (!$shrunk->acceptable()) {
            return $this->removeTail($value);
        }

        return $shrunk;
    }

    /**
     * @param Value<list<Value<mixed>>> $value
     *
     * @return Value<list<Value<mixed>>>
     */
    private function removeTail(Value $value): ?Value
    {
        $shrunk = $value->shrinkVia(static function($sequence) {
            $shrunk = $sequence;
            \array_pop($shrunk);

            return $shrunk;
        });

        if (!$shrunk->acceptable()) {
            return $this->removeHead($value);
        }

        return $shrunk->shrinkWith(new self(Strategy::recursiveTail));
    }

    /**
     * @param Value<list<Value<mixed>>> $value
     *
     * @return Value<list<Value<mixed>>>
     */
    private function removeHead(Value $value): ?Value
    {
        $shrunk = $value->shrinkVia(static function($sequence) {
            $shrunk = $sequence;
            \array_shift($shrunk);

            return $shrunk;
        });

        if (!$shrunk->acceptable()) {
            return $this->removeNth($value);
        }

        return $shrunk->shrinkWith(new self(Strategy::recursiveHead));
    }

    /**
     * @param Value<list<Value<mixed>>> $value
     * @param int<0, max> $n Default to 1 to express next one after head but accepts 0 due to self::$n
     *
     * @return Value<list<Value<mixed>>>
     */
    private function removeNth(Value $value, int $n = 1): ?Value
    {
        $shrunk = $value->maybeShrinkVia(static function(array $sequence) use ($n) {
            if (!\array_key_exists($n, $sequence)) {
                return null;
            }

            return [
                ...\array_slice($sequence, 0, $n),
                ...\array_slice($sequence, $n + 1),
            ];
        });

        if ($shrunk instanceof End) {
            return null;
        }

        if (\is_null($shrunk)) {
            return $this->shrinkANth($value);
        }

        if (!$shrunk->acceptable()) {
            return $this->shrinkANth($value);
        }

        return $shrunk->shrinkWith(new self(Strategy::recursiveNth, $n));
    }

    /**
     * @param Value<list<Value<mixed>>> $value
     * @param int<0, max> $n
     *
     * @return Value<list<Value<mixed>>>
     */
    private function shrinkANth(Value $value, int $n = 0): ?Value
    {
        $shrunk = $value->maybeShrinkVia(static function(array $sequence) use ($n) {
            /** @var list<Value<mixed>> $sequence */
            if (!\array_key_exists($n, $sequence)) {
                return End::instance;
            }

            $shrunk = $sequence[$n]->shrink()?->a();

            if (\is_null($shrunk)) {
                return null;
            }

            $sequence[$n] = $shrunk;

            return $sequence;
        });

        if ($shrunk instanceof End) {
            return $this->shrinkBNth($value);
        }

        if (\is_null($shrunk)) {
            return $this->shrinkANth($value, $n + 1);
        }

        if (!$shrunk->acceptable()) {
            return $this->shrinkANth($value, $n + 1);
        }

        return $shrunk->shrinkWith(new self(Strategy::recursiveNthShrink, $n));
    }

    /**
     * @param Value<list<Value<mixed>>> $value
     * @param int<0, max> $n
     *
     * @return Value<list<Value<mixed>>>
     */
    private function shrinkBNth(Value $value, int $n = 0): ?Value
    {
        $shrunk = $value->maybeShrinkVia(static function(array $sequence) use ($n) {
            /** @var list<Value<mixed>> $sequence */
            if (!\array_key_exists($n, $sequence)) {
                return End::instance;
            }

            $shrunk = $sequence[$n]->shrink()?->b();

            if (\is_null($shrunk)) {
                return null;
            }

            $sequence[$n] = $shrunk;

            return $sequence;
        });

        if ($shrunk instanceof End) {
            return null;
        }

        if (\is_null($shrunk)) {
            return $this->shrinkBNth($value, $n + 1);
        }

        if (!$shrunk->acceptable()) {
            return $this->shrinkBNth($value, $n + 1);
        }

        return $shrunk->shrinkWith(new self(Strategy::recursiveNthShrink, $n));
    }
}
