<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
};
use PHPUnit\Framework\Assert;

final class UpperBoundAtHundred implements Property
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
        return 'Counter can not go higher than 100';
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() > 98;
    }

    public function ensureHeldBy(object $counter): object
    {
        $counter->up();
        Assert::assertSame(100, $counter->current());

        return $counter;
    }
}
