<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Test\Report;

interface Runner
{
    public function __invoke(Test $test): Report;
}
