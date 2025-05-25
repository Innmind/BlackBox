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
final class Bridge implements ScenarioInterface
{
    /**
     * @param \Closure(...mixed): void $test
     * @param list<mixed> $args
     */
    private function __construct(
        private \Closure $test,
        private array $args,
    ) {
    }

    #[\Override]
    public function __invoke(Assert $assert): mixed
    {
        $refl = new \ReflectionProperty(TestCase::class, 'assert');
        $refl->setValue(null, $assert);

        try {
            ($this->test)(...$this->args);
        } catch (Failure|Assert\Failure $e) {
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
     * @param \Closure(...mixed): void $test
     * @param list<mixed> $args
     */
    public static function of(\Closure $test, array $args): self
    {
        return new self($test, $args);
    }

    /**
     * @return list<array{string, mixed}>
     */
    public function parameters(): array
    {
        $reflection = new \ReflectionFunction($this->test);
        /**
         * @psalm-suppress MixedArrayAccess
         * @var \Closure
         */
        $innerTest = $reflection->getClosureUsedVariables()['test'];
        $parameters = (new \ReflectionFunction($innerTest))->getParameters();

        $parameters = \array_map(
            static fn($parameter) => $parameter->getName(),
            $parameters,
        );
        $args = [];

        /** @var mixed $arg */
        foreach ($this->args as $index => $arg) {
            $args[] = [
                $parameters[$index] ?? 'undefined',
                $arg,
            ];
        }

        return $args;
    }
}
