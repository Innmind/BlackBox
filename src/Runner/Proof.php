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
    private Name $name;
    private Given $values;
    /** @var \Closure(Assert, ...mixed): void */
    private \Closure $test;
    /** @var list<\UnitEnum> */
    private array $tags;
    /** @var ?int<1, max> */
    private ?int $scenarii;
    private ?string $extraName;
    /** @var ?\Closure(): list<string> */
    private ?\Closure $nameParameters;

    /**
     * @psalm-mutation-free
     *
     * @param \Closure(Assert, ...mixed): void $test
     * @param list<\UnitEnum> $tags
     * @param ?int<1, max> $scenarii
     * @param ?\Closure(): list<string> $nameParameters
     */
    private function __construct(
        Name $name,
        Given $values,
        \Closure $test,
        array $tags,
        ?int $scenarii,
        ?string $extraName,
        ?\Closure $nameParameters,
    ) {
        $this->name = $name;
        $this->values = $values;
        $this->test = $test;
        $this->tags = $tags;
        $this->scenarii = $scenarii;
        $this->extraName = $extraName;
        $this->nameParameters = $nameParameters;
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
        );
    }

    /**
     * @return list<\UnitEnum>
     */
    public function tags(): array
    {
        return $this->tags;
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
            ->randomize()
            ->map(fn(array $args) => Scenario::of(
                $args,
                $this->test,
                $this->nameParameters,
            ))
            ->take($this->scenarii ?? $count);
    }
}
