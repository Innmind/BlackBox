<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\Assert;

/**
 * @template T of object
 */
interface Property
{
    /**
     * @return Set<static>|Set\Provider<static>
     */
    public static function any(): Set|Set\Provider;

    /**
     * @param T $systemUnderTest
     */
    public function applicableTo(object $systemUnderTest): bool;

    /**
     * @param T $systemUnderTest
     *
     * @return T The system under test with the property applied
     */
    public function ensureHeldBy(Assert $assert, object $systemUnderTest): object;
}
