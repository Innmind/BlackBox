<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

/**
 * @internal
 */
final class Stats
{
    /** @var int<0, max> */
    private int $proofs = 0;
    /** @var int<0, max> */
    private int $scenarii = 0;
    /** @var int<0, max> */
    private int $assertions = 0;
    /** @var int<0, max> */
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
     * @return int<0, max>
     */
    public function proofs(): int
    {
        return $this->proofs;
    }

    /**
     * @return int<0, max>
     */
    public function scenarii(): int
    {
        return $this->scenarii;
    }

    /**
     * @return int<0, max>
     */
    public function assertions(): int
    {
        return $this->assertions;
    }

    /**
     * @return int<0, max>
     */
    public function failures(): int
    {
        return $this->failures;
    }
}
