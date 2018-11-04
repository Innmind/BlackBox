<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner,
    Test,
    Test\Report,
};
use Innmind\OperatingSystem\OperatingSystem;

final class SameProcess implements Runner
{
    public function __invoke(OperatingSystem $os, Test $test): Report
    {
        return $test($os);
    }
}
