<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Runner\Proof,
    Runner\Printer,
    Runner\Arguments,
    Exception\LogicException,
};

final class Runner
{
    /** @var positive-int */
    private int $tests;
    private bool $enableShrinking;
    private Random $random;
    private Printer $printer;

    /**
     * @param positive-int $tests
     */
    public function __construct(
        int $tests,
        bool $enableShrinking,
        Random $random,
        Printer $printer
    ) {
        $this->tests = $tests;
        $this->enableShrinking = $enableShrinking;
        $this->random = $random;
        $this->printer = $printer;
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
            $this->printer->start($proof->name());
            $proof(
                $this->tests,
                $this->enableShrinking,
                $this->random,
                fn(string $name) => $this->printer->pass($name),
                fn() => $this->printer->held(),
                fn(string $name, string $reason, Arguments $arguments) => $this->printer->fail(
                    $name,
                    $reason,
                    $arguments,
                ),
            );
        }

        return $this->printer->terminate();
    }
}
