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
    /** @var \SplQueue<TestMethod> */
    private \SplQueue $tests;

    /**
     * @param \SplQueue<TestMethod> $tests
     */
    public function __construct(\SplQueue $tests)
    {
        $this->tests = $tests;
    }

    public function notify(Failed $event): void
    {
        $test = $event->test();

        if (!($test instanceof TestMethod)) {
            return;
        }

        $this->tests->enqueue($test);
    }
}
