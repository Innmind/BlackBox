<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Filter
{
    /** @var list<\UnitEnum> */
    private array $tags;

    /**
     * @param list<\UnitEnum> $tags
     */
    private function __construct(array $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param \Generator<Proof> $proofs
     *
     * @return \Generator<Proof>
     */
    public function __invoke(\Generator $proofs): \Generator
    {
        foreach ($proofs as $proof) {
            if (!$this->matchesTags($proof->tags())) {
                continue;
            }

            yield $proof;
        }
    }

    public static function new(): self
    {
        return new self([]);
    }

    /**
     * @psalm-mutation-free
     * @no-named-arguments
     */
    public function onTags(\UnitEnum ...$tags): self
    {
        return new self([...$this->tags, ...$tags]);
    }

    /**
     * @param list<\UnitEnum> $tags
     */
    private function matchesTags(array $tags): bool
    {
        foreach ($this->tags as $tag) {
            if (!\in_array($tag, $tags, true)) {
                return false;
            }
        }

        return true;
    }
}
