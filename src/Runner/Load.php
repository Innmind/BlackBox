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
        /** @psalm-suppress UnresolvableInclude Presume the developer uses a valid absolute path */
        yield from (require $path)($this);
    }

    /**
     * @return \Closure(): \Generator<Proof>
     */
    public static function from(string $path): \Closure
    {
        return static function() use ($path) {
            yield from (new self)($path);
        };
    }
}
