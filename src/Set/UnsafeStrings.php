<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};
use Innmind\Json\Json;

/**
 * @internal
 * @implements Implementation<string>
 */
final class UnsafeStrings implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     */
    private function __construct(private int $size)
    {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function implementation(): self
    {
        return new self(100);
    }

    /**
     * @deprecated Use Set::strings()->unsafe() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function any(): Set
    {
        return Set::strings()->unsafe();
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self($size);
    }

    #[\Override]
    public function values(Random $random, \Closure $predicate): \Generator
    {
        $json = \file_get_contents(__DIR__.'/unsafeStrings.json');

        if ($json === false) {
            throw new \RuntimeException('Unable to load unsafe strings dictionary');
        }

        /** @var list<string> */
        $values = Json::decode($json);
        $values = \array_values(\array_filter(
            $values,
            $predicate,
        ));

        if (\count($values) === 0) {
            throw new EmptySet;
        }

        $size = \count($values) - 1;
        $iterations = 0;

        while ($iterations < $this->size) {
            $index = $random->between(0, $size);
            $value = Value::of($values[$index])
                ->predicatedOn($predicate);

            yield $value->shrinkWith(UnsafeStrings\Shrinker::instance);
            ++$iterations;
        }
    }
}
