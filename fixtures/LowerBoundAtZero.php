<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
};
use PHPUnit\Framework\Assert;

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

    public function ensureHeldBy(object $counter): object
    {
        $counter->down();
        Assert::assertSame(0, $counter->current());

        return $counter;
    }
}
