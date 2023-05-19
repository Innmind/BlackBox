<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\Runner\Assert;

interface Scenario
{
    public function __invoke(Assert $assert): mixed;
}
