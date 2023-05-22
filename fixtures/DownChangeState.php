<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
};
use PHPUnit\Framework\Assert;

final class DownChangeState implements Property
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
