<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Zip;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
    Value\End,
};

/**
 * @internal
 * @implements Value\Shrinker<array{Value, Value}>
 */
enum Shrinker implements Value\Shrinker
{
    case instance;

    #[\Override]
    public function __invoke(Value $value): ?Dichotomy
    {
        return Dichotomy::of(
            $this->shrinkLeft($value),
            $this->shrinkRight($value),
        );
    }

    /**
     * @param Value<array{Value, Value}> $value
     *
     * @return ?Value<array{Value, Value}>
     */
    private function shrinkLeft(Value $value): ?Value
    {
        $shrunk = $value->maybeShrinkVia(static function($pair) {
            [$left, $right] = $pair;

            $dichotomy = $left->shrink();

            if (\is_null($dichotomy)) {
                return;
            }

            return [
                $dichotomy->a(),
                $right,
            ];
        });

        if ($shrunk === End::instance) {
            return null;
        }

        if (\is_null($shrunk)) {
            return $this->shrinkRight($value);
        }

        if (!$shrunk->acceptable()) {
            return $this->shrinkRight($value);
        }

        return $shrunk;
    }

    /**
     * @param Value<array{Value, Value}> $value
     *
     * @return ?Value<array{Value, Value}>
     */
    private function shrinkRight(Value $value): ?Value
    {
        $shrunk = $value->maybeShrinkVia(static function($pair) {
            [$left, $right] = $pair;

            $dichotomy = $right->shrink();

            if (\is_null($dichotomy)) {
                return;
            }

            return [
                $left,
                $dichotomy->a(),
            ];
        });

        if ($shrunk === End::instance) {
            return null;
        }

        if (\is_null($shrunk)) {
            return null;
        }

        if (!$shrunk->acceptable()) {
            return null;
        }

        return $shrunk;
    }
}
