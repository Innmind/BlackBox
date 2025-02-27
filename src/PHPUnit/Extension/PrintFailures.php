<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Extension;

use Innmind\BlackBox\{
    Runner\Proof\Scenario,
    Runner\IO,
    Set\Value,
};
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
    /** @var \WeakMap<TestMethod, array{callable, Value<Scenario>}> */
    private \WeakMap $scenarii;
    /** @var \SplQueue<TestMethod> */
    private \SplQueue $tests;
    private CliDumper $dumper;
    private VarCloner $cloner;

    /**
     * @param \WeakMap<TestMethod, array{callable, Value<Scenario>}> $scenarii
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

            $concrete = $scenario->unwrap();

            if (!($concrete instanceof Scenario\Inline)) {
                continue;
            }

            if (!$printed) {
                $output("\nFailing scenarii:\n\n");
                $printed = true;
            }

            // We need to re-analyse the parameters name because the ones
            // provided by the scenarion are the ones from the callable wrapper
            // to make the PHPUnit test work with BlackBox
            $reflection = new \ReflectionObject(\Closure::fromCallable($callable));
            $method = $reflection->getMethod('__invoke');
            $names = \array_map(
                static fn($parameter) => $parameter->getName(),
                $method->getParameters(),
            );
            $output("{$test->className()}::{$test->methodName()}\n");
            /** @var list<array{string, mixed}> */
            $parameters = [];

            /** @var mixed $value */
            foreach ($concrete->parameters() as $index => [$name, $value]) {
                $parameters[] = [
                    $names[$index] ?? 'undefined',
                    $value,
                ];
            }

            /** @var mixed $value */
            foreach ($parameters as [$name, $value]) {
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
