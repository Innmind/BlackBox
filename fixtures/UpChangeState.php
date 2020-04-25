<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\Property;
use PHPUnit\Framework\Assert;

final class UpChangeState implements Property
{
    public function name(): string
    {
        return 'Up change state';
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() < 100;
    }

    public function ensureHeldBy(object $counter): object
    {
        $initial = $counter->current();
        $counter->up();
        Assert::assertGreaterThan($initial, $counter->current());

        return $counter;
    }
}
