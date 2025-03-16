<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Map;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
    Value\End,
};

/**
 * @internal
 * @implements Value\Shrinker<mixed>
 */
enum Shrinker implements Value\Shrinker
{
    case instance;

    #[\Override]
    public function __invoke(Value $value): ?Dichotomy
    {
        $a = $value->maybeShrinkVia(static fn(Value $source) => $source->shrink()?->a());
        $b = $value->maybeShrinkVia(static fn(Value $source) => $source->shrink()?->b());

        if ($a instanceof End || !$a?->acceptable()) {
            $a = null;
        }

        if ($b instanceof End || !$b?->acceptable()) {
            $b = null;
        }

        return Dichotomy::of($a, $b);
    }
}
