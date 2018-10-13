<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\{
    Given\InitialValue,
    Given\SoFar,
    Given\Scenario,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
    Map,
};

final class Given
{
    private $scenarios;

    public function __construct(InitialValue ...$initialValues)
    {
        $initialValues = Stream::of(InitialValue::class, ...$initialValues);

        if ($initialValues->size() === 0) {
            $this->scenarios = Stream::of(
                Scenario::class,
                new Scenario(new Map('string', 'mixed'))
            );
        } else {
            $this->scenarios = $initialValues
                ->drop(1)
                ->reduce(
                    $initialValues->first(),
                    static function(InitialValue $dependency, InitialValue $initialValue): InitialValue {
                        return $initialValue->dependOn($dependency);
                    }
                )
                ->sets()
                ->reduce(
                    Stream::of(Scenario::class),
                    static function(StreamInterface $scenarios, SoFar $soFar): StreamInterface {
                        return $scenarios->add($soFar->scenario());
                    }
                );
        }
    }

    public function matrix(): StreamInterface
    {
        return $this->scenarios;
    }
}
