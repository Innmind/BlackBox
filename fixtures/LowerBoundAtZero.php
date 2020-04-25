<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\Property;
use PHPUnit\Framework\Assert;

final class LowerBoundAtZero implements Property
{
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
