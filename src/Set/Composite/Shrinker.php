<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
    Value\End,
};

/**
 * @internal
 * @implements Value\Shrinker<mixed>
 */
final class Shrinker implements Value\Shrinker
{
    /**
     * @param int<0, max> $n
     */
    private function __construct(
        private int $n = 0,
    ) {
    }

    #[\Override]
    public function __invoke(Value $value): ?Dichotomy
    {
        return Dichotomy::of(
            $this->shrinkANth($value, $this->n),
            $this->shrinkANth($value, $this->n + 1),
        );
    }

    public static function new(): self
    {
        return new self;
    }

    /**
     * @param int<0, max> $n
     */
    private function shrinkANth(Value $value, int $n): ?Value
    {
        $shrunk = $value->maybeShrinkVia(
            static fn(Combination $combination) => match ($combination->has($n)) {
                false => End::instance,
                true => $combination->aShrinkNth($n),
            },
        );

        if ($shrunk instanceof End) {
            return $this->shrinkBNth($value);
        }

        if (\is_null($shrunk)) {
            return $this->shrinkANth(
                $value,
                $n + 1,
            );
        }

        if (!$shrunk->acceptable()) {
            return $this->shrinkANth(
                $value,
                $n + 1,
            );
        }

        return $shrunk->shrinkWith(new self($n));
    }

    /**
     * @param int<0, max> $n
     */
    private function shrinkBNth(Value $value, int $n = 0): ?Value
    {
        $shrunk = $value->maybeShrinkVia(
            static fn(Combination $combination) => match ($combination->has($n)) {
                false => End::instance,
                true => $combination->bShrinkNth($n),
            },
        );

        if ($shrunk instanceof End) {
            return null;
        }

        if (\is_null($shrunk)) {
            return $this->shrinkBNth(
                $value,
                $n + 1,
            );
        }

        if (!$shrunk->acceptable()) {
            return $this->shrinkBNth(
                $value,
                $n + 1,
            );
        }

        return $shrunk->shrinkWith(new self($n));
    }
}
