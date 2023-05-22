<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
};
use PHPUnit\Framework\Assert;

final class UpChangeState implements Property
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
