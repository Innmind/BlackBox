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
                yield Proof::of(
                    Proof\Name::of('name does not matter'),
                    $this->given,
                    static fn($assert, ...$args) => $assert->not()->throws(
                        static fn() => $test(...$args),
                    ),
                    static fn() => \array_map(
                        static fn($parameter) => $parameter->getName(),
                        new \ReflectionFunction(\Closure::fromCallable($test))->getParameters(),
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
                    Extension::record($test, $failure->parameters());

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
                    $failure->parameters(),
                );
            }

            throw $failure;
        }
    }

    /**
     * @param callable(...mixed): void $test
     */
    #[\NoDiscard]
    public function prove(callable $test): BlackBox\Proof
    {
        return BlackBox\Proof::of(
            $this->given,
            $test,
        );
    }
}
