<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Load
{
    private function __construct()
    {
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     *
     * @return \Generator<Proof>
     */
    public function __invoke(string $path): \Generator
    {
        /**
         * @psalm-suppress UnresolvableInclude Presume the developer uses a valid absolute path
         * @var mixed $value
         */
        foreach ((require $path)($this) as $value) {
            if ($value instanceof Proof) {
                yield $value;
            }
        }
    }

    /**
     * @return \Closure(): \Generator<Proof>
     */
    #[\NoDiscard]
    public static function file(string $path): \Closure
    {
        return static function() use ($path) {
            yield from (new self)($path);
        };
    }

    /**
     * @return \Closure(): \Generator<Proof>
     */
    #[\NoDiscard]
    public static function directory(string $path): \Closure
    {
        return static function() use ($path) {
            $load = new self;
            $files = new \FilesystemIterator($path);

            /** @var \SplFileInfo $file */
            foreach ($files as $path => $file) {
                if ($file->isFile() && \is_string($path)) {
                    yield from $load($path);
                }
            }
        };
    }

    /**
     * @return \Closure(): \Generator<Proof>
     */
    #[\NoDiscard]
    public static function everythingIn(string $path): \Closure
    {
        return static function() use ($path) {
            $load = new self;
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path),
            );

            /**
             * @var string $path
             * @var \SplFileInfo $file
             */
            foreach ($files as $path => $file) {
                if ($file->isFile()) {
                    yield from $load($path);
                }
            }
        };
    }
}
