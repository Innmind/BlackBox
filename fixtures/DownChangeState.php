<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\Property;
use PHPUnit\Framework\Assert;

final class DownChangeState implements Property
{
    public function name(): string
    {
        return 'Down change state';
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() > 0;
    }

    public function ensureHeldBy(object $counter): object
    {
        $initial = $counter->current();
        $counter->down();
        Assert::assertLessThan($initial, $counter->current());

        return $counter;
    }
}
