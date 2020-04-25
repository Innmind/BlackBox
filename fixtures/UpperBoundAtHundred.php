<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\Property;
use PHPUnit\Framework\Assert;

final class UpperBoundAtHundred implements Property
{
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
