<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\Extension\CurrentTest,
    PHPUnit\Extension\SelectCurrentTest,
    PHPUnit\Extension\EraseCurrentTest,
    PHPUnit\Extension\RecordFailures,
    PHPUnit\Extension\RecordErrors,
    PHPUnit\Extension\PrintFailures,
    Runner\Proof\Scenario,
    Set\Value,
};
use PHPUnit\Runner\Extension\{
    Extension as ExtensionInterface,
    Facade,
    ParameterCollection,
};
use PHPUnit\TextUI\Configuration\Configuration;
use PHPUnit\Event\{
    Code\TestMethod,
};

final class Extension implements ExtensionInterface
{
    private static ?self $instance = null;

    private CurrentTest $currentTest;
    private SelectCurrentTest $selectCurrentTest;
    private EraseCurrentTest $eraseCurrentTest;
    /** @var \WeakMap<TestMethod, array{callable, Value<Scenario>}> */
    private \WeakMap $scenarii;
    /** @var \SplQueue<TestMethod> */
    private \SplQueue $tests;
    private RecordFailures $recordFailures;
    private RecordErrors $recordErrors;
    private PrintFailures $printFailures;

    public function __construct()
    {
        self::$instance = $this;
        $this->currentTest = new CurrentTest;
        $this->selectCurrentTest = new SelectCurrentTest($this->currentTest);
        $this->eraseCurrentTest = new EraseCurrentTest($this->currentTest);
        /** @var \WeakMap<TestMethod, array{callable, Value<Scenario>}> */
        $this->scenarii = new \WeakMap;
        /** @var \SplQueue<TestMethod> */
        $this->tests = new \SplQueue;
        $this->tests->setIteratorMode(\SplQueue::IT_MODE_FIFO);
        $this->recordFailures = new RecordFailures($this->tests);
        $this->recordErrors = new RecordErrors($this->tests);
        $this->printFailures = new PrintFailures($this->scenarii, $this->tests);
    }

    /**
     * @internal
     *
     * @param Value<Scenario> $scenario
     */
    public static function record(callable $callable, Value $scenario): void
    {
        if (\is_null(self::$instance)) {
            return;
        }

        $test = self::$instance->currentTest->get();

        if (\is_null($test)) {
            return;
        }

        self::$instance->scenarii->offsetSet($test, [$callable, $scenario]);
    }

    #[\Override]
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber($this->selectCurrentTest);
        $facade->registerSubscriber($this->eraseCurrentTest);
        $facade->registerSubscriber($this->recordFailures);
        $facade->registerSubscriber($this->recordErrors);
        $facade->registerSubscriber($this->printFailures);
    }
}
