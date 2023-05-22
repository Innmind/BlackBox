<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
};
use PHPUnit\Framework\Assert;

final class DownAndUpIsAnIdentityFunction implements Property
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
        return 'Down and up return to the initial value';
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() > 0;
    }

    public function ensureHeldBy(object $counter): object
    {
        $initial = $counter->current();
        $counter->down();
        $counter->up();
        Assert::assertSame($initial, $counter->current());

        return $counter;
    }
}
