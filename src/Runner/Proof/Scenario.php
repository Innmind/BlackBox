<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\Runner\Assert;

/**
 * @internal
 */
interface Scenario
{
    public function __invoke(Assert $assert): mixed;
}
