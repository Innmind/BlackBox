<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
    Runner\Assert,
};

final class LowerBoundAtZero implements Property
{
    /**
     * @return Set<self>
     */
    public static function any(): Set
    {
        return Set\Elements::of(new self);
    }

    public function name(): string
    {
        return 'Counter can not go lower than 0';
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() < 2;
    }

    public function ensureHeldBy(Assert $assert, object $counter): object
    {
        $counter->down();
        $assert->same(0, $counter->current());

        return $counter;
    }
}
