<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Set\Value;

final class When
{
    /** @var callable(...mixed): mixed */
    private $test;
    /** @var ?list<string> */
    private ?array $names = null;

    /**
     * @param callable(...mixed): mixed $test
     */
    public function __construct(callable $test)
    {
        $this->test = $test;
    }

    /**
     * @param Value<list<mixed>> $args
     */
    public function __invoke(Value $args): TestResult
    {
        try {
            return TestResult::of(($this->test)(...$args->unwrap()), $this->arguments($args));
        } catch (\Throwable $e) {
            return TestResult::throws($e, $this->arguments($args));
        }
    }

    /**
     * @param Value<list<mixed>> $args
     */
    private function arguments(Value $args): Arguments
    {
        return new Arguments($args, $this->names());
    }

    /**
     * @return list<string>
     */
    private function names(): array
    {
        if (!\is_null($this->names)) {
            return $this->names;
        }

        $test = \Closure::fromCallable($this->test);
        $reflection = new \ReflectionObject($test);
        $reflection = $reflection->getMethod('__invoke');

        return $this->names = \array_map(
            static fn(\ReflectionParameter $parameter): string => $parameter->getName(),
            $reflection->getParameters(),
        );
    }
}
