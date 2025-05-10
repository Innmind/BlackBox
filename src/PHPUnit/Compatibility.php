<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Application,
    Runner\Assert,
    Runner\Given,
    Runner\Proof,
    Runner\Proof\Scenario,
    Set\Value,
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
     * @param positive-int $size
     */
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
    public function asDataProvider(): iterable
    {
        $values = $this->given->set()->values(Random::default);

        foreach ($values as $value) {
            yield $value->unwrap();
        }
    }

    /**
     * @param callable(...mixed): void $test
     */
    public function then(callable $test): void
    {
        /** @var \SplQueue<array{mixed, Value<Scenario>}> */
        $failures = new \SplQueue;
        $printer = new ExtractFailure($failures);

        $_ = $this
            ->app
            ->usePrinter($printer)
            ->tryToProve(function() use ($test) {
                yield Proof\Inline::of(
                    Proof\Name::of('name does not matter here'),
                    $this->given,
                    function($assert, ...$args) use ($test) {
                        $assert->not()->throws(
                            static fn() => $test(...$args),
                        );
                        // This is here to force capturing the $this context and
                        // for the cs fixer to not enforce a static callable.
                        // The capturing is used in Proof\Scenario\Inline to
                        // determine the correct list of arguments
                        $_ = $this;
                    },
                );
            });

        if (!$failures->isEmpty()) {
            /** @var mixed $failure */
            [$failure, $scenario] = $failures->dequeue();

            if ($failure instanceof Assert\Failure && $this->blackbox) {
                throw Scenario\Failure::of($failure, $scenario);
            }

            if ($failure instanceof \Throwable) {
                Extension::record($test, $scenario);

                throw $failure;
            }
        }
    }

    /**
     * @param callable(...mixed): void $test
     */
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
}
