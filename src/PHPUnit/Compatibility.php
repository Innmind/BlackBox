<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Application,
    Runner\Assert,
    Runner\Given,
    Runner\Proof,
    Runner\Proof\Scenario,
    Runner\IO\Collect,
    Random,
    PHPUnit\Framework\TestCase,
};

final class Compatibility
{
    private Application $app;
    private Given $given;
    private bool $blackbox;

    private function __construct(Application $app, Given $given, bool $blackbox)
    {
        $this->app = $app;
        $this->given = $given;
        $this->blackbox = $blackbox;
    }

    /**
     * @internal
     */
    public static function phpunit(Application $app, Given $given): self
    {
        return new self($app, $given, false);
    }

    /**
     * @internal
     */
    public static function blackbox(Application $app, Given $given): self
    {
        return new self($app, $given, true);
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
    public function disableShrinking(): self
    {
        return new self(
            $this->app->disableShrinking(),
            $this->given,
            $this->blackbox,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     */
    #[\NoDiscard]
    public function take(int $size): self
    {
        return new self(
            $this->app->scenariiPerProof($size),
            $this->given,
            $this->blackbox,
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(...mixed): bool $predicate
     */
    #[\NoDiscard]
    public function filter(callable $predicate): self
    {
        return new self(
            $this->app,
            $this->given->filter($predicate),
            $this->blackbox,
        );
    }

    /**
     * This method returns an iterable that can be used as a PHPUnit data provider
     *
     * Note that using this feature you won't be able to use shrinking
     *
     * @return iterable<list<mixed>>
     */
    #[\NoDiscard]
    public function asDataProvider(): iterable
    {
        $values = $this->given->set()->take(100)->values(Random::default);

        foreach ($values as $value) {
            yield $value->unwrap();
        }
    }

    /**
     * @param callable(...mixed): void $test
     */
    public function then(callable $test): void
    {
        $io = Collect::new();
        $failures = $this
            ->app
            ->displayOutputVia($io)
            ->displayErrorVia($io)
            ->failures(function() use ($test) {
                yield Proof\Inline::of(
                    Proof\Name::of('name does not matter'),
                    $this->given,
                    static fn($assert, ...$args) => $assert->not()->throws(
                        static fn() => $test(...$args),
                    ),
                );
            });

        if (!$this->blackbox) {
            // BlackBox here can only return a failure if the user test has
            // thrown an exception.
            foreach ($failures as $failure) {
                $kind = $failure->assertion()->kind();

                if (!($kind instanceof Assert\Failure\Property)) {
                    continue;
                }

                /** @var mixed */
                $error = $kind->value();

                if ($error instanceof \Throwable) {
                    $scenario = self::testArgs($test, $failure->scenario());

                    Extension::record(
                        $test,
                        [...$scenario, ...$failure->debug()],
                    );

                    throw $error;
                }
            }

            return;
        }

        foreach ($failures as $failure) {
            $kind = $failure->assertion()->kind();

            if (
                $kind instanceof Assert\Failure\Property &&
                $kind->value() instanceof Assert\Failure
            ) {
                throw Scenario\Failure::from(
                    $kind->value(),
                    self::testArgs($test, $failure->scenario()),
                    $failure->debug(),
                );
            }

            throw Scenario\Failure::from(
                $failure->assertion(),
                self::testArgs($test, $failure->scenario()),
                $failure->debug(),
            );
        }
    }

    /**
     * @param callable(...mixed): void $test
     */
    #[\NoDiscard]
    public function prove(callable $test): BlackBox\Proof
    {
        $wrapped = function(mixed ...$args) use ($test): void {
            /** @psalm-suppress RedundantCondition Scope is changed below */
            if (!($this instanceof TestCase)) {
                throw new \LogicException('Test must be inside an instance of '.TestCase::class);
            }

            $this->executeClosure($test, \array_values($args));
        };
        $refl = new \ReflectionFunction(\Closure::fromCallable($test));
        /** @var \Closure(...mixed): void */
        $wrapped = $wrapped->bindTo($refl->getClosureThis());

        return BlackBox\Proof::of(
            $this->given,
            $wrapped,
        );
    }

    /**
     * @param list<array{string, mixed}> $scenario
     *
     * @return list<array{string, mixed}>
     */
    private static function testArgs(callable $test, array $scenario): array
    {
        $reflection = new \ReflectionFunction(\Closure::fromCallable($test));
        $names = \array_map(
            static fn($parameter) => $parameter->getName(),
            $reflection->getParameters(),
        );
        /** @var list<array{string, mixed}> */
        $parameters = [];

        /** @var mixed $value */
        foreach ($scenario as $index => [$_, $value]) {
            $parameters[] = [
                $names[$index] ?? 'undefined',
                $value,
            ];
        }

        return $parameters;
    }
}
