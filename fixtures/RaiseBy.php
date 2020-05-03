<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\Property;
use PHPUnit\Framework\Assert;

final class RaiseBy implements Property
{
    private int $raise;

    public function __construct(int $raise)
    {
        $this->raise = $raise;
    }

    public function name(): string
    {
        return 'Raise by '.$this->raise;
    }

    public function applicableTo(object $counter): bool
    {
        return $counter->current() < 100;
    }

    public function ensureHeldBy(object $counter): object
    {
        $initial = $counter->current();

        for ($i = 0; $i < $this->raise; $i++) {
            $counter->up();
        }

        Assert::assertGreaterThan($initial, $counter->current());

        return $counter;
    }
}
