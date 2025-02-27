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
    private CurrentTest $currentTest;

    public function __construct(CurrentTest $currentTest)
    {
        $this->currentTest = $currentTest;
    }

    #[\Override]
    public function notify(Finished $event): void
    {
        $this->currentTest->erase();
    }
}
