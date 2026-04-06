<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit\Framework\Attributes;

/**
 * @immutable
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final readonly class DataProvider
{
    /**
     * @var non-empty-string
     */
    private string $methodName;

    /**
     * @param non-empty-string $methodName
     */
    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }

    /**
     * @return non-empty-string
     */
    public function methodName(): string
    {
        return $this->methodName;
    }
}
