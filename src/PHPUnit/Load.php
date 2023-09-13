<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    PHPUnit\Framework\TestCase,
    Tag,
};
use PHPUnit\Framework\Attributes\{
    DataProvider,
    Group,
};

final class Load
{
    private string $path;
    /** @var callable(string): ?\UnitEnum */
    private $parseTag;

    /**
     * @psalm-mutation-free
     *
     * @param callable(string): ?\UnitEnum $parseTag
     */
    private function __construct(string $path, callable $parseTag)
    {
        $this->path = $path;
        $this->parseTag = $parseTag;
    }

    public function __invoke()
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->path),
        );

        /**
         * @var string $path
         * @var \SplFileInfo $file
         */
        foreach ($files as $path => $file) {
            if ($file->isFile() && \str_ends_with($path, 'Test.php')) {
                /** @psalm-suppress UnresolvableInclude */
                require_once $path;
            }
        }

        foreach (\get_declared_classes() as $class) {
            if (!\is_a($class, TestCase::class, true)) {
                continue;
            }

            $refl = new \ReflectionClass($class);

            foreach ($refl->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if (!\str_starts_with($method->getName(), 'test')) {
                    continue;
                }

                $attributes = $method->getAttributes(DataProvider::class);
                $groups = \array_map(
                    static fn($group) => $group->newInstance()->name(),
                    $method->getAttributes(Group::class),
                );
                $tags = \array_values(\array_filter(
                    \array_map(
                        $this->parseTag,
                        $groups,
                    ),
                    static fn($tag) => $tag instanceof \UnitEnum,
                ));

                if (isset($attributes[0])) {
                    $provider = $attributes[0]->newInstance()->methodName();

                    /**
                     * @var int|string $name
                     * @var list<mixed> $data
                     */
                    foreach ([$class, $provider]() as $name => $data) {
                        $test = Proof::of($class, $method->getName(), $data);

                        if (\is_string($name)) {
                            $test = $test->named($name);
                        }

                        yield $test->tag(...$tags);
                    }

                    continue;
                }

                yield Proof::of($class, $method->getName())->tag(...$tags);
            }
        }
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(string): ?\UnitEnum $parser
     */
    public function parseTagWith(callable $parser): self
    {
        return new self(
            $this->path,
            fn(string $name) => $parser($name) ?? ($this->parseTag)($name),
        );
    }

    public static function directory(string $path): self
    {
        return new self($path, Tag::of(...));
    }

    public static function testsAt(string $path): \Generator
    {
        yield from self::directory($path)();
    }
}
