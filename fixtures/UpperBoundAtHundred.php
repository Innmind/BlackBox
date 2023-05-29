<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
    Runner\Assert,
};

final class UpperBoundAtHundred implements Property
{
    /**
     * @return Set<self>
     */
    public static function any(): Set
    {
        return Set\Elements::of(new self);
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() > 98;
    }

    public function ensureHeldBy(Assert $assert, object $counter): object
    {
        $counter->up();
        $assert->same(100, $counter->current());

        return $counter;
    }
}
