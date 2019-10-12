<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Colour\{
    Colour as Colours,
    RGBA,
};

final class Colour
{
    /**
     * @return Set<RGBA>
     */
    public static function of(): Set
    {
        return Elements::of(
            ...Colours::literals()->values()
        );
    }
}
