<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class When
{
    /** @var callable(...mixed): mixed */
    private $test;
    private ?Arguments $arguments = null;

    /**
     * @param callable(...mixed): mixed $test
     */
    public function __construct(callable $test)
    {
        $this->test = $test;
    }

    /**
     * @param mixed $args
     */
    public function __invoke(...$args): TestResult
    {
        try {
            return TestResult::of(($this->test)(...$args), $this->arguments());
        } catch (\Throwable $e) {
            return TestResult::throws($e, $this->arguments());
        }
    }

    private function arguments(): Arguments
    {
        if (!\is_null($this->arguments)) {
            return $this->arguments;
        }

        $test = \Closure::fromCallable($this->test);
        $reflection = new \ReflectionObject($test);
        $reflection = $reflection->getMethod('__invoke');

        return $this->arguments = new Arguments(\array_map(
            static fn(\ReflectionParameter $parameter): string => $parameter->getName(),
            $reflection->getParameters(),
        ));
    }
}
