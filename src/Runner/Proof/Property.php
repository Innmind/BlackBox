<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Runner\Given,
    Runner\Assert,
    Property as Concrete,
};

final class Property
{
    /**
     * @psalm-pure
     *
     * @param class-string<Concrete> $property
     * @param Set<callable(): object> $systemUnderTest
     */
    public static function of(
        string $property,
        Set $systemUnderTest,
    ): Proof {
        /** @var Set<Concrete> */
        $propertySet = ([$property, 'any'])();

        return Inline::of(
            Name::of($property),
            Given::of(Set::tuple(
                $propertySet,
                $systemUnderTest,
            )),
            static function($assert, Concrete $property, callable $factory) {
                /** @var object */
                $sut = $factory();
                $assert->debug('systemUnderTest', $sut);

                if (!$property->applicableTo($sut)) {
                    $assert->fail('The property is not applicable to the system under test.');
                }

                try {
                    $property->ensureHeldBy($assert, $sut);
                } catch (Assert\Failure $e) {
                    throw $e;
                } catch (\Throwable $e) {
                    $assert->not()->throws(static fn() => throw $e);
                }
            },
        );
    }
}
