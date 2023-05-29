<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Extension;

use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\{
    PreparedSubscriber,
    Prepared,
};

/**
 * @internal
 */
final class SelectCurrentTest implements PreparedSubscriber
{
    private CurrentTest $currentTest;

    public function __construct(CurrentTest $currentTest)
    {
        $this->currentTest = $currentTest;
    }

    public function notify(Prepared $event): void
    {
        $test = $event->test();

        if ($test instanceof TestMethod) {
            $this->currentTest->set($test);
        }
    }
}
