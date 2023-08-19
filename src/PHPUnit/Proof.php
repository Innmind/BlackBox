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
    /** @var list<\UnitEnum> */
    private array $tags;

    /**
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     * @param list<\UnitEnum> $tags
     */
    private function __construct(string $class, string $method, array $tags)
    {
        $this->class = $class;
        $this->method = $method;
        $this->tags = $tags;
    }

    /**
     * @param class-string<TestCase> $class
     * @param non-empty-string $method
     */
    public static function of(string $class, string $method): self
    {
        return new self($class, $method, []);
    }

    public function name(): Name
    {
        return Name::of(\sprintf(
            '%s::%s',
            $this->class,
            $this->method,
        ));
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    public function tag(\UnitEnum ...$tags): self
    {
        return new self(
            $this->class,
            $this->method,
            [...$this->tags, ...$tags],
        );
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function scenarii(int $count): Set
    {
        /** @var Set<Scenario> */
        return Set\Elements::of(Proof\Scenario::of($this->class, $this->method))->take(1);
    }
}
