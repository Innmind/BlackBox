<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
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
     */
    private function __construct()
    {
    }

    #[\Override]
    public function __invoke(
        Random $random,
        \Closure $predicate,
    ): \Generator {
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
            return;
        }

        $maxSize = \count($values) - 1;

        while (true) {
            $index = $random->between(0, $maxSize);
            $value = Value::of($values[$index])
                ->predicatedOn($predicate);

            yield $value->shrinkWith(UnsafeStrings\Shrinker::instance);
        }
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function implementation(): self
    {
        return new self;
    }

    /**
     * @deprecated Use Set::strings()->unsafe() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    #[\NoDiscard]
    public static function any(): Set
    {
        return Set::strings()->unsafe();
    }
}
