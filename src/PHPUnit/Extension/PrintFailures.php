<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Extension;

use Innmind\BlackBox\Runner\IO;
use PHPUnit\Event\{
    Application\FinishedSubscriber,
    Application\Finished,
    Code\TestMethod,
};
use Symfony\Component\VarDumper\{
    Dumper\CliDumper,
    Cloner\VarCloner,
};

/**
 * @internal
 */
final class PrintFailures implements FinishedSubscriber
{
    /** @var \WeakMap<TestMethod, array{callable, list<array{string, mixed}>}> */
    private \WeakMap $scenarii;
    /** @var \SplQueue<TestMethod> */
    private \SplQueue $tests;
    private CliDumper $dumper;
    private VarCloner $cloner;

    /**
     * @param \WeakMap<TestMethod, array{callable, list<array{string, mixed}>}> $scenarii
     * @param \SplQueue<TestMethod> $tests
     */
    public function __construct(\WeakMap $scenarii, \SplQueue $tests)
    {
        $this->scenarii = $scenarii;
        $this->tests = $tests;
        $this->dumper = new CliDumper;
        $this->cloner = new VarCloner;
        $this->dumper->setColors(true);
    }

    #[\Override]
    public function notify(Finished $event): void
    {
        /** @var callable(string): void */
        $output = IO\Standard::output;
        $printed = false;

        foreach ($this->tests as $test) {
            [$callable, $scenario] = $this->scenarii[$test] ?? [null, null];

            if (\is_null($scenario) || \is_null($callable)) {
                continue;
            }

            if (!$printed) {
                $output("\nFailing scenarii:\n\n");
                $printed = true;
            }

            $output("{$test->className()}::{$test->methodName()}\n");

            /** @var mixed $value */
            foreach ($scenario as [$name, $value]) {
                $output(\sprintf(
                    '$%s = %s',
                    $name,
                    $this->dump($value),
                ));
            }

            $output("\n{$test->file()}:{$test->line()}\n\n");
        }
    }

    private function dump(mixed $value): string
    {
        return $this->dumper->dump(
            $this->cloner->cloneVar(
                $value,
            ),
            true,
        ) ?? '';
    }
}
