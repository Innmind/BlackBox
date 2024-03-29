<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
    Runner\Assert,
};

final class UpAndDownIsAnIdentityFunction implements Property
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
        return $counter->current() < 99;
    }

    public function ensureHeldBy(Assert $assert, object $counter): object
    {
        $initial = $counter->current();
        $counter->up();
        $counter->down();
        $assert->same($initial, $counter->current());

        return $counter;
    }
}
