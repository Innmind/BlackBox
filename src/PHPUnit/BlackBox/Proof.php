<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\BlackBox;

use Innmind\BlackBox\Runner\Given;

final class Proof
{
    /**
     * @param \Closure(...mixed): void $test
     */
    private function __construct(
        private Given $given,
        private \Closure $test,
    ) {
    }

    /**
     * @internal
     *
     * @param callable(...mixed): void $test
     */
    public static function of(Given $given, callable $test): self
    {
        return new self($given, \Closure::fromCallable($test));
    }

    /**
     * @internal
     */
    public function given(): Given
    {
        return $this->given;
    }

    /**
     * @internal
     *
     * @return \Closure(...mixed): void
     */
    public function test(): \Closure
    {
        return $this->test;
    }
}
