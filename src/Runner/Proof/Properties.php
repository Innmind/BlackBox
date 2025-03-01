<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\{
    Set,
    Runner\Proof,
    Properties as Concrete,
};

final class Properties implements Proof
{
    private Name $name;
    /** @var Set<Concrete> */
    private Set $properties;
    /** @var Set<object> */
    private Set $systemUnderTest;
    /** @var list<\UnitEnum> */
    private array $tags;

    /**
     * @param Set<Concrete> $properties
     * @param Set<object> $systemUnderTest
     * @param list<\UnitEnum> $tags
     */
    private function __construct(
        Name $name,
        Set $properties,
        Set $systemUnderTest,
        array $tags,
    ) {
        $this->name = $name;
        $this->properties = $properties;
        $this->systemUnderTest = $systemUnderTest;
        $this->tags = $tags;
    }

    /**
     * @param Set<Concrete> $properties
     * @param Set<object> $systemUnderTest
     */
    public static function of(
        Name $name,
        Set $properties,
        Set $systemUnderTest,
    ): self {
        return new self($name, $properties, $systemUnderTest, []);
    }

    #[\Override]
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    #[\Override]
    public function tag(\UnitEnum ...$tags): self
    {
        return new self(
            $this->name,
            $this->properties,
            $this->systemUnderTest,
            [...$this->tags, ...$tags],
        );
    }

    #[\Override]
    public function tags(): array
    {
        return $this->tags;
    }

    #[\Override]
    public function scenarii(int $count): Set
    {
        /** @var Set<Scenario> */
        return Set::randomize(Set::of(Set\Composite::immutable(
            Scenario\Properties::of(...),
            $this->properties,
            $this->systemUnderTest,
        )))->take($count);
    }
}
