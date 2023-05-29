<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

/**
 * @internal
 */
final class Stats
{
    /** @var 0|positive-int */
    private int $proofs = 0;
    /** @var 0|positive-int */
    private int $scenarii = 0;
    /** @var 0|positive-int */
    private int $assertions = 0;
    /** @var 0|positive-int */
    private int $failures = 0;

    private function __construct()
    {
    }

    public static function new(): self
    {
        return new self;
    }

    public function incrementProofs(): void
    {
        ++$this->proofs;
    }

    public function incrementScenarii(): void
    {
        ++$this->scenarii;
    }

    public function incrementAssertions(): void
    {
        ++$this->assertions;
    }

    public function incrementFailures(): void
    {
        ++$this->failures;
    }

    public function successful(): bool
    {
        return $this->failures === 0;
    }

    /**
     * @return 0|positive-int
     */
    public function proofs(): int
    {
        return $this->proofs;
    }

    /**
     * @return 0|positive-int
     */
    public function scenarii(): int
    {
        return $this->scenarii;
    }

    /**
     * @return 0|positive-int
     */
    public function assertions(): int
    {
        return $this->assertions;
    }

    /**
     * @return 0|positive-int
     */
    public function failures(): int
    {
        return $this->failures;
    }
}
