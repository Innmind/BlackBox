<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Given;

final class Matrix
{
    private $set;
    private $dependency;

    public function __construct(Set $set)
    {
        $this->set = $set;
    }

    public function dot(Set $set): self
    {
        $matrix = new self($set);
        $matrix->dependency = $this;

        return $matrix;
    }

    /**
     * @return StreamInterface<MapInterface<string, mixed>> [description]
     */
    public function scenarios(): StreamInterface
    {
        if (\is_null($this->dependency)) {
            return $this->set->reduce(
                Stream::of(MapInterface::class),
                function(StreamInterface $scenarios, $value): StreamInterface {
                    return $scenarios->add(
                        Map::of('string', 'mixed')
                            ($this->set->name(), $value)
                    );
                }
            );
        }

        return $this->set->reduce(
            Stream::of(MapInterface::class),
            function(StreamInterface $scenarios, $value): StreamInterface {
                return $scenarios->append(
                    $this
                        ->dependency
                        ->scenarios()
                        ->map(function(MapInterface $scenario) use ($value): MapInterface {
                            return $scenario->put(
                                $this->set->name(),
                                $value
                            );
                        })
                );
            }
        );
    }
}
