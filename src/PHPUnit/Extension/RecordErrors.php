<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Extension;

use PHPUnit\Event\{
    Code\TestMethod,
    Test\Errored,
    Test\ErroredSubscriber,
};

/**
 * @internal
 */
final class RecordErrors implements ErroredSubscriber
{
    /**
     * @param \SplQueue<TestMethod> $tests
     */
    public function __construct(private \SplQueue $tests)
    {
    }

    #[\Override]
    public function notify(Errored $event): void
    {
        $test = $event->test();

        if (!($test instanceof TestMethod)) {
            return;
        }

        $this->tests->enqueue($test);
    }
}
