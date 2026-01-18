<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure\Property,
};

/**
 * Using the french name as "object" is a reserved keyword
 */
final class Objet
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

    #[\NoDiscard]
    public function not(): Objet\Not
    {
        return Objet\Not::of($this->stats, $this->object);
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

        // Making sure the class exists allows to use aliases and autoload them
        if (!\class_exists($class) && !\interface_exists($class)) {
            throw Failure::of(Property::of(
                $this->object,
                \sprintf(
                    'Class, alias or interface %s does not exist',
                    $class,
                ),
            ));
        }

        if (!($this->object instanceof $class)) {
            throw Failure::of(Property::of(
                $this->object,
                $message ?? \sprintf(
                    'Failed to assert an object is an instance of %s',
                    $class,
                ),
            ));
        }

        return $this;
    }
}
