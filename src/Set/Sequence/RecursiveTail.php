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
final class RecursiveTail
{
    private static ?self $instance = null;

    private function __construct()
    {
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
            RemoveTail::of($value),
            RemoveHead::of($value),
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
    public static function of(Value $value): ?Dichotomy
    {
        return self::instance()($value);
    }

    public static function instance(): self
    {
        return self::$instance ??= new self;
    }
}
