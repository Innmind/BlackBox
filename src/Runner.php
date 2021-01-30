<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Runner\Proof,
    Runner\Printer,
    Runner\TestResult,
    Exception\LogicException,
};

final class Runner
{
    /** @var positive-int */
    private int $tests;
    private bool $enableShrinking;
    private Random $random;
    private Printer $printer;
    private string $filter;

    /**
     * @param positive-int $tests
     */
    public function __construct(
        int $tests,
        bool $enableShrinking,
        Random $random,
        Printer $printer,
        string $filter
    ) {
        $this->tests = $tests;
        $this->enableShrinking = $enableShrinking;
        $this->random = $random;
        $this->printer = $printer;
        $this->filter = $filter;
    }

    /**
     * @return bool True if it failed
     */
    public function __invoke(string $pathToProofs): bool
    {
        $wrongFactoryType = new LogicException('Proofs file must return callable(): Generator<Innmind\BlackBox\Runner\Proof>');
        /** @psalm-suppress UnresolvableInclude */
        $factory = require $pathToProofs;

        if (!\is_callable($factory)) {
            throw $wrongFactoryType;
        }

        $proofs = $factory();

        if (!$proofs instanceof \Generator) {
            throw $wrongFactoryType;
        }

        $this->printer->begin();

        /** @var Proof $proof */
        foreach ($proofs as $proof) {
            if (!$proof->matches($this->filter)) {
                continue;
            }

            $this->printer->start($proof->name());
            /**
             * @psalm-suppress ArgumentTypeCoercion For the shrinking strategy
             * @psalm-suppress MixedArgumentTypeCoercion For the trace
             */
            $proof(
                $this->tests,
                $this->enableShrinking,
                $this->random,
                fn(string $name) => $this->printer->pass($name),
                fn() => $this->printer->held(),
                fn(string $name, string $strategy) => $this->printer->shrinking($name, $strategy),
                fn(string $name, string $reason, TestResult $result, array $trace) => $this->printer->fail(
                    $name,
                    $reason,
                    $result,
                    $trace,
                ),
            );
        }

        return $this->printer->terminate();
    }
}
