<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Runner\Proof\Name,
    Runner\Proof\Scenario,
    Set,
};

interface Proof
{
    public function name(): Name;

    /**
     * @return Set<Scenario>
     */
    public function scenarii(): Set;
}
