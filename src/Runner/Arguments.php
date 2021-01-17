<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class Arguments
{
    /** @var list<string> */
    private array $names;

    /**
     * @param list<string> $names [description]
     */
    public function __construct(array $names)
    {
        $this->names = $names;
    }

    /**
     * @return list<string>
     */
    public function names(): array
    {
        return $this->names;
    }
}
