<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\Framework\TestCase,
    Runner\Proof\Name,
    Runner\Proof\Inline,
    Runner\Proof\Scenario\Failure,
    Runner\Assert,
    Runner\Stats,
};

final class Proof
{
    /**
     * @internal
     *
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     * @param list<mixed> $args
     */
    public static function of(string $class, string $method, array $args = []): Inline
    {
        $name = Name::of(\sprintf(
            '%s::%s',
            $class,
            $method,
        ));

        $refl = new \ReflectionMethod($class, $method);
        $return = (string) $refl->getReturnType();

        if ($return !== BlackBox\Proof::class) {
            return Inline::test(
                $name,
                static function($assert) use ($class, $method, $args) {
                    try {
                        $test = new ($class)($assert);
                        $test->executeTest($method, $args);
                    } catch (Failure|Assert\Failure $e) {
                        throw $e;
                    } catch (\Throwable $e) {
                        $assert->not()->throws(static function() use ($e) {
                            throw $e;
                        });
                    }
                },
            );
        }

        // The true Assert instance is injected below
        $test = new ($class)(Assert::of(
            Stats::new(),
            Assert\Debug::new(),
        ));
        /** @var BlackBox\Proof */
        $proof = $test->{$method}(...$args);
        $test = $proof->test();

        return Inline::of(
            $name,
            $proof->given(),
            static function($assert, ...$args) use ($test) {
                $refl = new \ReflectionProperty(TestCase::class, 'assert');
                $refl->setValue(null, $assert);

                $wrapped = function(mixed ...$args) use ($test): void {
                    /**
                     * @psalm-suppress RedundantCondition Scope is changed below
                     * @psalm-suppress InvalidScope
                     */
                    if (!($this instanceof TestCase)) {
                        throw new \LogicException('Test must be inside an instance of '.TestCase::class);
                    }

                    $this->executeClosure($test, \array_values($args));
                };
                $refl = new \ReflectionFunction($test);
                /** @var \Closure(...mixed): void */
                $wrapped = $wrapped->bindTo($refl->getClosureThis());

                try {
                    ($wrapped)(...$args);
                } catch (Failure|Assert\Failure $e) {
                    throw $e;
                } catch (\Throwable $e) {
                    $assert->not()->throws(static fn() => throw $e);
                }
            },
            static fn() => $parameters = \array_map(
                static fn($parameter) => $parameter->getName(),
                new \ReflectionFunction($test)->getParameters(),
            ), // todo Proof->nameParameters() once the Scenario is no longer an interface
        );
    }
}
