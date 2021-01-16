<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class When
{
    /** @var callable(...mixed): mixed */
    private $test;

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
            return TestResult::of(($this->test)(...$args));
        } catch (\Throwable $e) {
            return TestResult::throws($e);
        }
    }
}
