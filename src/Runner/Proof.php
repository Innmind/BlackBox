<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Set,
    Runner\Proof\Name,
    Runner\Proof\Scenario,
    Property,
    Properties,
};

final class Proof
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(Assert, ...mixed): void $test
     * @param list<\UnitEnum> $tags
     * @param ?int<1, max> $scenarii
     * @param ?\Closure(): list<string> $nameParameters
     */
    private function __construct(
        private Name $name,
        private Given $values,
        private \Closure $test,
        private array $tags,
        private ?int $scenarii,
        private ?string $extraName,
        private ?\Closure $nameParameters,
        private bool $disableShrinking,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param \Closure(Assert, ...mixed): void $test
     * @param ?\Closure(): list<string> $nameParameters
     */
    public static function of(
        Name $name,
        Given $values,
        \Closure $test,
        ?\Closure $nameParameters = null,
    ): self {
        return new self(
            $name,
            $values,
            $test,
            [],
            null,
            null,
            $nameParameters,
            false,
        );
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param \Closure(Assert): void $test
     */
    public static function test(
        Name $name,
        \Closure $test,
    ): self {
        return new self(
            $name,
            Given::of(Set::of([])),
            $test,
            [],
            1,
            null,
            static fn() => [],
            false,
        );
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param class-string<Property> $property
     * @param Set<callable(): object> $systemUnderTest
     */
    public static function property(
        string $property,
        Set $systemUnderTest,
    ): self {
        /** @var Set<Property> */
        $propertySet = ([$property, 'any'])();

        return self::of(
            Name::of($property),
            Given::of(Set::tuple(
                $propertySet,
                $systemUnderTest,
            )),
            static function($assert, Property $property, callable $factory) {
                /** @var object */
                $sut = $factory();
                $assert->debug('systemUnderTest', $sut);

                if (!$property->applicableTo($sut)) {
                    $assert->fail('The property is not applicable to the system under test.');
                }

                $property->ensureHeldBy($assert, $sut);
            },
        );
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param Set<Properties> $properties
     * @param Set<callable(): object> $systemUnderTest
     */
    public static function properties(
        Name $name,
        Set $properties,
        Set $systemUnderTest,
    ): self {
        return self::of(
            $name,
            Given::of(Set::tuple(
                $properties,
                $systemUnderTest,
            )),
            static function($assert, Properties $properties, callable $factory) {
                /** @var object */
                $sut = $factory();
                $assert->debug('systemUnderTest', $sut);

                $properties->ensureHeldBy($assert, $sut);
            },
        );
    }

    public function name(): Name
    {
        return match ($this->extraName) {
            null => $this->name,
            default => Name::of(\sprintf(
                '%s(%s)',
                $this->name->toString(),
                $this->extraName,
            )),
        };
    }

    /**
     * @psalm-mutation-free
     */
    #[\NoDiscard]
    public function named(string $name): self
    {
        return new self(
            $this->name,
            $this->values,
            $this->test,
            $this->tags,
            $this->scenarii,
            $name,
            $this->nameParameters,
            $this->disableShrinking,
        );
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    #[\NoDiscard]
    public function tag(\UnitEnum ...$tags): self
    {
        return new self(
            $this->name,
            $this->values,
            $this->test,
            [...$this->tags, ...$tags],
            $this->scenarii,
            $this->extraName,
            $this->nameParameters,
            $this->disableShrinking,
        );
    }

    /**
     * @param int<1, max> $take
     */
    #[\NoDiscard]
    public function take(int $take): self
    {
        return new self(
            $this->name,
            $this->values,
            $this->test,
            $this->tags,
            $take,
            $this->extraName,
            $this->nameParameters,
            $this->disableShrinking,
        );
    }

    /**
     * @return list<\UnitEnum>
     */
    public function tags(): array
    {
        return $this->tags;
    }

    public function tagged(\UnitEnum $tag): bool
    {
        return \in_array($tag, $this->tags, true);
    }

    #[\NoDiscard]
    public function disableShrinking(): self
    {
        return new self(
            $this->name,
            $this->values,
            $this->test,
            $this->tags,
            $this->scenarii,
            $this->extraName,
            $this->nameParameters,
            true,
        );
    }

    /**
     * @internal
     *
     * @param int<1, max> $count
     *
     * @return Set<Scenario>
     */
    public function scenarii(int $count): Set
    {
        return $this
            ->values
            ->set()
            ->via(fn($set) => match ($this->disableShrinking) {
                true => $set->disableShrinking(),
                false => $set,
            })
            ->randomize()
            ->map(fn(array $args) => Scenario::of(
                $args,
                $this->test,
                $this->nameParameters,
            ))
            ->take($this->scenarii ?? $count);
    }
}
