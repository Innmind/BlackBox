<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Immutable\Sequence as Structure;

/**
 * {@inheritdoc}
 */
final class Sequence implements Set
{
    private $set;
    private $sizes;
    private $predicate;

    public function __construct(Set $set, Integers $sizes = null)
    {
        $this->set = $set;
        $this->sizes = ($sizes ?? Integers::between(0, 100))->take(100);
        $this->predicate = static function(): bool {
            return true;
        };
    }

    public static function of(Set $set, Integers $sizes = null): self
    {
        return new self($set, $sizes);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->sizes = $this->sizes->take($size);

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->predicate = function($value) use ($predicate): bool {
            if (!($this->predicate)($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * @return \Generator<Structure>
     */
    public function values(): \Generator
    {
        foreach ($this->sizes->values() as $size) {
            $sequence = new Structure;
            $values = $this->set->take($size)->values();

            while ($sequence->size() < $size) {
                $sequence = $sequence->add($values->current());
                $values->next();
            }

            if (!($this->predicate)($sequence)) {
                continue;
            }

            yield $sequence;
        }
    }
}
