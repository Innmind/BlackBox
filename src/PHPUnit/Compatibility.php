<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Application,
    Runner\Given,
    Runner\Proof,
};

final class Compatibility
{
    private Application $app;
    private Given $given;

    /**
     * @internal
     */
    public function __construct(Application $app, Given $given)
    {
        $this->app = $app;
        $this->given = $given;
    }

    /**
     * @psalm-mutation-free
     */
    public function disableShrinking(): self
    {
        return new self(
            $this->app->disableShrinking(),
            $this->given,
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
        );
    }

    /**
     * @param callable(...mixed): void $test
     */
    public function then(callable $test): void
    {
        $failures = new \SplQueue;
        $printer = new ExtractFailure($failures);

        $_ = $this
            ->app
            ->usePrinter($printer)
            ->tryToProve(function() use ($test) {
                yield Proof\Inline::of(
                    Proof\Name::of('name does not matter here'),
                    $this->given,
                    static function($assert, ...$args) use ($test) {
                        $assert->not()->throws(
                            static fn() => $test(...$args),
                        );
                    },
                );
            });

        if (!$failures->isEmpty()) {
            /** @var mixed */
            $failure = $failures->dequeue();

            if ($failure instanceof \Throwable) {
                throw $failure;
            }
        }
    }
}
