<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Given\InitialValue,
    Given\SoFar,
    Given\Scenario,
};
use Innmind\Immutable\Stream;

final class Given
{
    private $scenarios;

    public function __construct(InitialValue ...$initialValues)
    {
        $initialValues = Stream::of(InitialValue::class, ...$initialValues);

        if ($initialValues->size() === 0) {
            $this->scenarios = (function() {
                yield new SoFar;
            })();
        } else {
            $this->scenarios = $initialValues
                ->drop(1)
                ->reduce(
                    $initialValues->first(),
                    static function(InitialValue $dependency, InitialValue $initialValue): InitialValue {
                        return $initialValue->dependOn($dependency);
                    }
                )
                ->sets();
        }
    }

    /**
     * @return \Generator<Scenario>
     */
    public function scenarios(): \Generator
    {
        foreach ($this->scenarios as $soFar) {
            yield $soFar->scenario();
        }
    }
}
