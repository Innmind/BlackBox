<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Extension;

use PHPUnit\Event\Test\{
    FinishedSubscriber,
    Finished,
};

/**
 * @internal
 */
final class EraseCurrentTest implements FinishedSubscriber
{
    public function __construct(private CurrentTest $currentTest)
    {
    }

    #[\Override]
    public function notify(Finished $event): void
    {
        $this->currentTest->erase();
    }
}
