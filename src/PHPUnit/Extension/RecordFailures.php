<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Extension;

use PHPUnit\Event\{
    Code\TestMethod,
    Test\Failed,
    Test\FailedSubscriber,
};

/**
 * @internal
 */
final class RecordFailures implements FailedSubscriber
{
    /**
     * @param \SplQueue<TestMethod> $tests
     */
    public function __construct(private \SplQueue $tests)
    {
    }

    #[\Override]
    public function notify(Failed $event): void
    {
        $test = $event->test();

        if (!($test instanceof TestMethod)) {
            return;
        }

        $this->tests->enqueue($test);
    }
}
