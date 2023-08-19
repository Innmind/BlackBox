<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Proof;

use Innmind\BlackBox\{
    PHPUnit\TestCase,
    Runner\Assert,
    Runner\Proof\Scenario as ScenarioInterface,
    Runner\Proof\Scenario\Failure,
};

/**
 * @internal
 */
final class Scenario implements ScenarioInterface
{
    /** @var class-string<TestCase> */
    private string $class;
    /** @var non-empty-string */
    private string $method;

    /**
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     */
    private function __construct(string $class, string $method)
    {
        $this->class = $class;
        $this->method = $method;
    }

    public function __invoke(Assert $assert): mixed
    {
        try {
            $test = new ($this->class)($assert);
            $test->executeTest($this->method);
        } catch (Failure $e) {
            throw $e;
        } catch (\Throwable $e) {
            $assert->not()->throws(static function() use ($e) {
                throw $e;
            });
        }

        return null;
    }

    /**
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     */
    public static function of(string $class, string $method): self
    {
        return new self($class, $method);
    }
}
