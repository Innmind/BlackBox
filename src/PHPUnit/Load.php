<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class Load
{
    private string $path;

    private function __construct(string $path)
    {
        $this->path = $path;
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

                        yield $test;
                    }

                    continue;
                }

                yield Proof::of($class, $method->getName());
            }
        }
    }

    public static function directory(string $path): self
    {
        return new self($path);
    }

    public static function testsAt(string $path): \Generator
    {
        yield from self::directory($path)();
    }
}