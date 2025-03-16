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
final class RecursiveNthShrink
{
    /**
     * @param int<0, max> $n
     */
    private function __construct(
        private int $n = 0,
    ) {
    }

    /**
     * @internal
     * @template A
     *
     * @param Value<list<Value<A>>> $value
     *
     * @return ?Dichotomy<list<A>>
     */
    public function __invoke(Value $value): ?Dichotomy
    {
        if (\count($value->unwrap()) === 0) {
            return null;
        }

        if (!$value->map(Detonate::of(...))->acceptable()) {
            return null;
        }

        return Dichotomy::of(
            ShrinkANth::of($value, $this->n),
            ShrinkANth::of($value, $this->n + 1),
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
    public static function of(Value $value, int $n = 0): ?Dichotomy
    {
        return (new self($n))($value);
    }
}
