<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

final class Properties
{
    /** @var list<Property> */
    private array $properties;

    public function __construct(Property $first, Property ...$properties)
    {
        $this->properties = [$first, ...$properties];
    }

    /**
     * @throws \Exception Any exception understood by your test framework
     *
     * @return object The system under test with the property applied
     */
    public function ensureHeldBy(object $systemUnderTest): object
    {
        foreach ($this->properties as $property) {
            if ($property->applicableTo($systemUnderTest)) {
                $systemUnderTest = $property->ensureHeldBy($systemUnderTest);
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
