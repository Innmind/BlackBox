<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\Property;
use PHPUnit\Framework\Assert;

final class UpAndDownIsAnIdentityFunction implements Property
{
    public function name(): string
    {
        return 'Up and down return to the initial value';
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() < 99;
    }

    public function ensureHeldBy(object $counter): object
    {
        $initial = $counter->current();
        $counter->up();
        $counter->down();
        Assert::assertSame($initial, $counter->current());

        return $counter;
    }
}
