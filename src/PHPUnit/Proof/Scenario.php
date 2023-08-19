<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Proof;

use Innmind\BlackBox\{
    PHPUnit\Framework\TestCase,
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
    /** @var list<mixed> */
    private array $args;

    /**
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     * @param list<mixed> $args
     */
    private function __construct(string $class, string $method, array $args)
    {
        $this->class = $class;
        $this->method = $method;
        $this->args = $args;
    }

    public function __invoke(Assert $assert): mixed
    {
        try {
            $test = new ($this->class)($assert);
            $test->executeTest($this->method, $this->args);
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
     * @internal
     *
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     * @param list<mixed> $args
     */
    public static function of(string $class, string $method, array $args): self
    {
        return new self($class, $method, $args);
    }
}
