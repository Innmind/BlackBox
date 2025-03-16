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
final class RecursiveNth
{
    /**
     * @param int<1, max> $n
     */
    private function __construct(
        private int $n = 1,
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
            RemoveNth::of($value, $this->n),
            RemoveNth::of($value, $this->n + 1),
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
    public static function of(Value $value, int $n = 1): ?Dichotomy
    {
        return (new self($n))($value);
    }
}
