<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\Assert;

/**
 * @template T of object
 */
final class Properties
{
    /** @var list<Property<T>> */
    private array $properties;

    /**
     * @no-named-arguments
     *
     * @param Property<T> $first
     * @param Property<T> $properties
     */
    private function __construct(Property $first, Property ...$properties)
    {
        $this->properties = [$first, ...$properties];
    }

    /**
     * @no-named-arguments
     *
     * @template A of object
     *
     * @param Property<A> $first
     * @param Property<A> $properties
     *
     * @return self<A>
     */
    public static function of(Property $first, Property ...$properties): self
    {
        return new self($first, ...$properties);
    }

    /**
     * @param T $systemUnderTest
     *
     * @return T The system under test with the property applied
     */
    public function ensureHeldBy(Assert $assert, object $systemUnderTest): object
    {
        foreach ($this->properties as $property) {
            if ($property->applicableTo($systemUnderTest)) {
                $systemUnderTest = $property->ensureHeldBy($assert, $systemUnderTest);
            }
        }

        return $systemUnderTest;
    }

    /**
     * @return list<Property<T>>
     */
    public function properties(): array
    {
        return $this->properties;
    }
}
