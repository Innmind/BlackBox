<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

use Innmind\BlackBox\{
    Property,
    Set,
};
use PHPUnit\Framework\Assert;

final class RaiseBy implements Property
{
    private int $raise;

    public function __construct(int $raise)
    {
        $this->raise = $raise;
    }

    /**
     * @return Set<self>
     */
    public static function any(): Set
    {
        return Set\Decorate::immutable(
            static fn(int $raise) => new self($raise),
            Set\Integers::between(1, 99),
        );
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
