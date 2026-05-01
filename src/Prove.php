<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Proof,
    Set\Collapse,
    Set\Provider,
};

final class Prove
{
    private function __construct(
    ) {
    }

    /**
     * @internal
     */
    public static function new(): self
    {
        return new self;
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     */
    public function proof(string $name): Prove\Proof
    {
        return Prove\Proof::of(Proof\Name::of($name));
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     * @param callable(Assert): void $test
     */
    public function test(string $name, callable $test): Proof
    {
        return Proof::test(
            Proof\Name::of($name),
            \Closure::fromCallable($test),
        );
    }

    /**
     * @psalm-pure
     *
     * @param class-string<Property> $property
     * @param Set<callable(): object>|Provider<callable(): object> $systemUnderTest
     */
    public function property(
        string $property,
        Set|Provider $systemUnderTest,
    ): Proof {
        return Proof::property($property, Collapse::of($systemUnderTest));
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     * @param Set<Properties>|Provider<Properties> $properties
     * @param Set<callable(): object>|Provider<callable(): object> $systemUnderTest
     */
    public function properties(
        string $name,
        Set|Provider $properties,
        Set|Provider $systemUnderTest,
    ): Proof {
        return Proof::properties(
            Proof\Name::of($name),
            Collapse::of($properties),
            Collapse::of($systemUnderTest),
        );
    }
}
