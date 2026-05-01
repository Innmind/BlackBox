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
    public function __construct(private CurrentTest $currentTest)
    {
    }

    #[\Override]
    public function notify(Prepared $event): void
    {
        $test = $event->test();

        if ($test instanceof TestMethod) {
            $this->currentTest->set($test);
        }
    }
}
