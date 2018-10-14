<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner,
    Test,
    Test\Report,
};

final class SameProcess implements Runner
{
    public function __invoke(Test $test): Report
    {
        return $test();
    }
}
