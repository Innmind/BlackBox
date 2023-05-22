<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Runner\Assert;

final class Properties
{
    /** @var list<Property> */
    private array $properties;

    /**
     * @no-named-arguments
     */
    public function __construct(Property $first, Property ...$properties)
    {
        $this->properties = [$first, ...$properties];
    }

    /**
     * @return object The system under test with the property applied
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
     * @return list<Property>
     */
    public function properties(): array
    {
        return $this->properties;
    }
}
