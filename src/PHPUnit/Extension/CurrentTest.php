<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Extension;

use PHPUnit\Event\Code\TestMethod;

/**
 * @internal
 */
final class CurrentTest
{
    private ?TestMethod $test = null;

    public function set(TestMethod $test): void
    {
        $this->test = $test;
    }

    public function get(): ?TestMethod
    {
        return $this->test;
    }

    public function erase(): void
    {
        $this->test = null;
    }
}
