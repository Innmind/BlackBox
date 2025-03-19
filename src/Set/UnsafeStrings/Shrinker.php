<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\UnsafeStrings;

use Innmind\BlackBox\Set\{
    Dichotomy,
    Value,
};

/**
 * @internal
 * @implements Value\Shrinker<string>
 */
enum Shrinker implements Value\Shrinker
{
    case instance;

    #[\Override]
    public function __invoke(Value $value): ?Dichotomy
    {
        if ($value->unwrap() === '') {
            return null;
        }

        return Dichotomy::of(
            $this->removeTrailingCharacter($value),
            $this->removeLeadingCharacter($value),
        );
    }

    /**
     * @param Value<string> $value
     *
     * @return ?Value<string>
     */
    private function removeTrailingCharacter(Value $value): ?Value
    {
        $shrunk = $value->shrinkVia(static fn($string) => \mb_substr(
            $string,
            0,
            -1,
            'ASCII',
        ));

        if (!$shrunk->acceptable()) {
            return null;
        }

        return $shrunk;
    }

    /**
     * @param Value<string> $value
     *
     * @return ?Value<string>
     */
    private function removeLeadingCharacter(Value $value): ?Value
    {
        $shrunk = $value->shrinkVia(static fn($string) => \mb_substr(
            $string,
            1,
            null,
            'ASCII',
        ));

        if (!$shrunk->acceptable()) {
            return null;
        }

        return $shrunk;
    }
}
