<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\Assert;

interface Property
{
    /**
     * @return Set<static>
     */
    public static function any(): Set;
    public function applicableTo(object $systemUnderTest): bool;

    /**
     * @return object The system under test with the property applied
     */
    public function ensureHeldBy(Assert $assert, object $systemUnderTest): object;
}
