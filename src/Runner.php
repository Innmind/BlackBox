<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Test\Report;
use Innmind\OperatingSystem\OperatingSystem;

interface Runner
{
    public function __invoke(OperatingSystem $os, Test $test): Report;
}
