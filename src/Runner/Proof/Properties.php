<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Given,
    Runner\Assert,
    Properties as Concrete,
};

final class Properties
{
    /**
     * @psalm-pure
     *
     * @param Set<Concrete> $properties
     * @param Set<callable(): object> $systemUnderTest
     */
    public static function of(
        Name $name,
        Set $properties,
        Set $systemUnderTest,
    ): Proof {
        return Proof::of(
            $name,
            Given::of(Set::tuple(
                $properties,
                $systemUnderTest,
            )),
            static function($assert, Concrete $properties, callable $factory) {
                /** @var object */
                $sut = $factory();
                $assert->debug('systemUnderTest', $sut);

                try {
                    $properties->ensureHeldBy($assert, $sut);
                } catch (Assert\Failure $e) {
                    throw $e;
                } catch (\Throwable $e) {
                    $assert->not()->throws(static fn() => throw $e);
                }
            },
        );
    }
}
