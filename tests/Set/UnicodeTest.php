<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Unicode,
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
};
use PHPUnit\Framework\Attributes\DataProvider;

class UnicodeTest extends TestCase
{
    use BlackBox;

    #[DataProvider('blocks')]
    public function testBlocks($block)
    {
        $this
            ->forAll($block)
            ->then(function($string) {
                $this->assertIsString($string);
            });
    }

    public static function blocks()
    {
        $methods = \get_class_methods(Unicode::class);

        foreach ($methods as $method) {
            yield $method => [Unicode::{$method}()];
        }
    }
}
