<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\Framework\TestCase,
    Runner\Proof as ProofInterface,
    Runner\Proof\Name,
    Runner\Proof\Inline,
    Runner\Proof\Scenario\Failure,
    Runner\Assert,
    Runner\Stats,
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
    public static function of(string $class, string $method, array $args = []): ProofInterface
    {
        $refl = new \ReflectionMethod($class, $method);
        $return = (string) $refl->getReturnType();

        if ($return !== BlackBox\Proof::class) {
            return Inline::test(
                Name::of(\sprintf(
                    '%s::%s',
                    $class,
                    $method,
                )),
                static function($assert) use ($class, $method, $args) {
                    try {
                        $test = new ($class)($assert);
                        $test->executeTest($method, $args);
                    } catch (Failure|Assert\Failure $e) {
                        throw $e;
                    } catch (\Throwable $e) {
                        $assert->not()->throws(static function() use ($e) {
                            throw $e;
                        });
                    }
                },
            );
        }

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

    /**
     * @psalm-mutation-free
     */
    #[\Override]
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
        // The true Assert instance is injected in Proof\Bridge
        $test = new ($this->class)(Assert::of(
            Stats::new(),
            Assert\Debug::new(),
        ));
        /** @var BlackBox\Proof */
        $proof = $test->{$this->method}(...$this->args);

        return $proof
            ->given()
            ->set()
            ->map(static fn($args) => Proof\Bridge::of(
                $proof->test(),
                $args,
            ))
            ->randomize()
            ->take($count);
    }
}
