<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Objet;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure,
    Assert\Failure\Property,
};

final class Not
{
    private Stats $stats;
    private object $object;

    private function __construct(Stats $stats, object $object)
    {
        $this->stats = $stats;
        $this->object = $object;
    }

    /**
     * @internal
     */
    public static function of(Stats $stats, object $object): self
    {
        return new self($stats, $object);
    }

    /**
     * @param class-string $class
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function instance(string $class, ?string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($this->object instanceof $class) {
            throw Failure::of(Property::of(
                $this->object,
                $message ?? \sprintf(
                    'Failed to assert an object is not an instance of %s',
                    $class,
                ),
            ));
        }

        return $this;
    }
}
