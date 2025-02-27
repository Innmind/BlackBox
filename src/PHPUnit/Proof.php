<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\Framework\TestCase,
    Runner\Proof as ProofInterface,
    Runner\Proof\Name,
    Runner\Proof\Scenario,
    Set,
};

final class Proof implements ProofInterface
{
    /** @var class-string<TestCase> */
    private string $class;
    /** @var non-empty-string */
    private string $method;
    private ?string $name;
    /** @var list<mixed> */
    private array $args;
    /** @var list<\UnitEnum> */
    private array $tags;

    /**
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     * @param list<mixed> $args
     * @param list<\UnitEnum> $tags
     */
    private function __construct(
        string $class,
        string $method,
        ?string $name,
        array $args,
        array $tags,
    ) {
        $this->class = $class;
        $this->method = $method;
        $this->name = $name;
        $this->args = $args;
        $this->tags = $tags;
    }

    /**
     * @internal
     *
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     * @param list<mixed> $args
     */
    public static function of(string $class, string $method, array $args = []): self
    {
        return new self($class, $method, null, $args, []);
    }

    #[\Override]
    public function name(): Name
    {
        return Name::of(\sprintf(
            '%s::%s%s',
            $this->class,
            $this->method,
            match ($this->name) {
                null => '',
                default => "({$this->name})",
            },
        ));
    }

    public function named(string $name): self
    {
        return new self(
            $this->class,
            $this->method,
            $name,
            $this->args,
            $this->tags,
        );
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    #[\Override]
    public function tag(\UnitEnum ...$tags): self
    {
        return new self(
            $this->class,
            $this->method,
            $this->name,
            $this->args,
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
        return Set\Elements::of(Proof\Scenario::of(
            $this->class,
            $this->method,
            $this->args,
        ))->take(1);
    }
}
