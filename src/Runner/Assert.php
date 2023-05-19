<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Assert
{
    private function __construct()
    {
    }

    /**
     * @param callable(): bool $assertion
     *
     * @throws Failure
     */
    public function that(callable $assertion): void
    {
        if (!$assertion()) {
            throw new Failure;
        }
    }
}
