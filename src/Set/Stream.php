<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Immutable\Stream as Structure;

/**
 * {@inheritdoc}
 * @template I
 */
final class Stream implements Set
{
    private $type;
    private $set;
    private $sizes;
    private $predicate;

    public function __construct(string $type, Set $set, Integers $sizes = null)
    {
        $this->type = $type;
        $this->set = $set;
        $this->sizes = ($sizes ?? Integers::of(0, 100))->take(100);
        $this->predicate = static function(): bool {
            return true;
        };
    }

    /**
     * @return Set<Structure<I>>
     */
    public static function of(string $type, Set $set, Integers $sizes = null): self
    {
        return new self($type, $set, $sizes);
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
     * @return \Generator<Structure<I>>
     */
    public function values(): \Generator
    {
        foreach ($this->sizes->values() as $size) {
            $stream = new Structure($this->type);
            $values = $this->set->take($size)->values();

            while ($stream->size() < $size) {
                $stream = $stream->add($values->current());
                $values->next();
            }

            if (!($this->predicate)($stream)) {
                continue;
            }

            yield $stream;
        }
    }
}
