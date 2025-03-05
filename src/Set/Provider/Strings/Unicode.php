<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider\Strings;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\MadeOf,
};

/**
 * @see https://unicode-table.com/en/blocks/
 * @implements Provider<string>
 */
final class Unicode implements Provider
{
    /**
     * @psalm-mutation-free
     */
    private function __construct()
    {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function char(): Set
    {
        $methods = \get_class_methods(self::class);
        $methods = \array_filter(
            $methods,
            static fn(string $method): bool => !\in_array(
                $method,
                [
                    '__construct',
                    'of',
                    'char',
                    'between',
                    'atLeast',
                    'atMost',
                    'take',
                    'filter',
                    'map',
                    'flatMap',
                    'toSet',
                    'block',
                ],
                true,
            ),
        );
        /**
         * @psalm-suppress MixedReturnStatement
         * @psalm-suppress MixedInferredReturnType
         * @psalm-suppress ImpureFunctionCall
         * @var non-empty-list<Set<string>>
         */
        $sets = \array_map(
            fn(string $method): Set => $this->{$method}(),
            $methods,
        );

        return Set::either(...$sets);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<0, max> $min
     * @param int<1, max> $max
     *
     * @return Set<string>
     */
    public function between(int $min, int $max): Set
    {
        return MadeOf::of($this->char())->between($min, $max);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $min
     *
     * @return Set<non-empty-string>
     */
    public function atLeast(int $min): Set
    {
        return MadeOf::of($this->char())->atLeast($min);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $max
     *
     * @return Set<string>
     */
    public function atMost(int $max): Set
    {
        return MadeOf::of($this->char())->atMost($max);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function controlCharater(): Set
    {
        return $this->block(0x0000, 0X001F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function basicLatin(): Set
    {
        return $this->block(0x0020, 0x007F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function latin1Supplement(): Set
    {
        return $this->block(0x0080, 0x00FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function latinExtendedA(): Set
    {
        return $this->block(0x0100, 0x017F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function latinExtendedB(): Set
    {
        return $this->block(0x0180, 0x024F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ipaExtensions(): Set
    {
        return $this->block(0x0250, 0x02AF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function spacingModifierLetters(): Set
    {
        return $this->block(0x02B0, 0x02FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function combiningDiacriticalMarks(): Set
    {
        return $this->block(0x0300, 0x036F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function greekAndCoptic(): Set
    {
        return $this->block(0x0370, 0x03FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cyrillic(): Set
    {
        return $this->block(0x0400, 0x04FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cyrillicSupplement(): Set
    {
        return $this->block(0x0500, 0x052F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function armenian(): Set
    {
        return $this->block(0x0530, 0x058F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hebrew(): Set
    {
        return $this->block(0x0590, 0x05FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function arabic(): Set
    {
        return $this->block(0x0600, 0x06FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function syriac(): Set
    {
        return $this->block(0x0700, 0x074F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function arabicSupplement(): Set
    {
        return $this->block(0x0750, 0x077F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function thaana(): Set
    {
        return $this->block(0x0780, 0x07BF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function nko(): Set
    {
        return $this->block(0x07C0, 0x07FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function samaritan(): Set
    {
        return $this->block(0x0800, 0x083F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mandaic(): Set
    {
        return $this->block(0x0840, 0x085F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function syriacSupplement(): Set
    {
        return $this->block(0x0860, 0x086F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function arabicExtendedA(): Set
    {
        return $this->block(0x08A0, 0x08FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function devanagari(): Set
    {
        return $this->block(0x0900, 0x097F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function bengali(): Set
    {
        return $this->block(0x0980, 0x09FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function gurmukhi(): Set
    {
        return $this->block(0x0A00, 0x0A7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function gujarati(): Set
    {
        return $this->block(0x0A80, 0x0AFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oriya(): Set
    {
        return $this->block(0x0B00, 0x0B7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tamil(): Set
    {
        return $this->block(0x0B80, 0x0BFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function telugu(): Set
    {
        return $this->block(0x0C00, 0x0C7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kannada(): Set
    {
        return $this->block(0x0C80, 0x0CFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function malayalam(): Set
    {
        return $this->block(0x0D00, 0x0D7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function sinhala(): Set
    {
        return $this->block(0x0D80, 0x0DFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function thai(): Set
    {
        return $this->block(0x0E00, 0x0E7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function lao(): Set
    {
        return $this->block(0x0E80, 0x0EFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tibetan(): Set
    {
        return $this->block(0x0F00, 0x0FFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function myanmar(): Set
    {
        return $this->block(0x1000, 0x109F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function georgian(): Set
    {
        return $this->block(0x10A0, 0x10FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hangulJamo(): Set
    {
        return $this->block(0x1100, 0x11FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ethiopic(): Set
    {
        return $this->block(0x1200, 0x137F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ethiopicSupplement(): Set
    {
        return $this->block(0x1380, 0x139F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cherokee(): Set
    {
        return $this->block(0x13A0, 0x13FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function unifiedCanadianAboriginalSyllabics(): Set
    {
        return $this->block(0x1400, 0x167F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ogham(): Set
    {
        return $this->block(0x1680, 0x169F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function runic(): Set
    {
        return $this->block(0x16A0, 0x16FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tagalog(): Set
    {
        return $this->block(0x1700, 0x171F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hanunoo(): Set
    {
        return $this->block(0x1720, 0x173F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function buhid(): Set
    {
        return $this->block(0x1740, 0x175F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tagbanwa(): Set
    {
        return $this->block(0x1760, 0x177F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function khmer(): Set
    {
        return $this->block(0x1780, 0x17FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mongolian(): Set
    {
        return $this->block(0x1800, 0x18AF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function unifiedCanadianAboriginalSyllabicsExtended(): Set
    {
        return $this->block(0x18B0, 0x18FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function limbu(): Set
    {
        return $this->block(0x1900, 0x194F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function taiLe(): Set
    {
        return $this->block(0x1950, 0x197F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function newTaiLue(): Set
    {
        return $this->block(0x1980, 0x19DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function khmerSymbols(): Set
    {
        return $this->block(0x19E0, 0x19FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function buginese(): Set
    {
        return $this->block(0x1A00, 0x1A1F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function taiTham(): Set
    {
        return $this->block(0x1A20, 0x1AAF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function combiningDiacriticalMarksExtended(): Set
    {
        return $this->block(0x1AB0, 0x1AFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function balinese(): Set
    {
        return $this->block(0x1B00, 0x1B7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function sundanese(): Set
    {
        return $this->block(0x1B80, 0x1BBF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function batak(): Set
    {
        return $this->block(0x1BC0, 0x1BFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function lepcha(): Set
    {
        return $this->block(0x1C00, 0x1C4F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function olChiki(): Set
    {
        return $this->block(0x1C50, 0x1C7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cyrillicExtendedC(): Set
    {
        return $this->block(0x1C80, 0x1C8F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function sundaneseSupplement(): Set
    {
        return $this->block(0x1CC0, 0x1CCF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function vedicExtensions(): Set
    {
        return $this->block(0x1CD0, 0x1CFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function phoneticExtensions(): Set
    {
        return $this->block(0x1D00, 0x1D7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function phoneticExtensionsSupplement(): Set
    {
        return $this->block(0x1D80, 0x1D8F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function combiningDiacriticalMarksSupplement(): Set
    {
        return $this->block(0x1DC0, 0x1DFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function latinExtendedAdditional(): Set
    {
        return $this->block(0x1E00, 0x1EFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function greekExtended(): Set
    {
        return $this->block(0x1F00, 0x1FFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function generalPunctuation(): Set
    {
        return $this->block(0x2000, 0x206F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function superscriptsAndSubscripts(): Set
    {
        return $this->block(0x2070, 0x209F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function currencySymbols(): Set
    {
        return $this->block(0x20A0, 0x20CF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function combiningDiacriticalMarksForSymbols(): Set
    {
        return $this->block(0x20D0, 0x20FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function letterlikeSymbols(): Set
    {
        return $this->block(0x2100, 0x214F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function numberForms(): Set
    {
        return $this->block(0x2150, 0x218F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function arrows(): Set
    {
        return $this->block(0x2190, 0x21FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mathematicalOperators(): Set
    {
        return $this->block(0x2200, 0x22FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function miscellaneousTechnical(): Set
    {
        return $this->block(0x2300, 0x23FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function controlPictures(): Set
    {
        return $this->block(0x2400, 0x243F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function opticalCharacterRecognition(): Set
    {
        return $this->block(0x2440, 0x245F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function enclosedAlphanumerics(): Set
    {
        return $this->block(0x2460, 0x247F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function boxDrawing(): Set
    {
        return $this->block(0x2500, 0x257F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function blockElements(): Set
    {
        return $this->block(0x2580, 0x259F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function geometricShapes(): Set
    {
        return $this->block(0x25A0, 0x25FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function miscellaneousSymbols(): Set
    {
        return $this->block(0x2600, 0x26FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function dingbats(): Set
    {
        return $this->block(0x270, 0x27BF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function miscellaneousMathematicalSymbolsA(): Set
    {
        return $this->block(0x27C0, 0x27EF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function supplementalArrowsA(): Set
    {
        return $this->block(0x27F0, 0x27FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function braillePatterns(): Set
    {
        return $this->block(0x2800, 0x28FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function supplementalArrowsB(): Set
    {
        return $this->block(0x2900, 0x297F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function miscellaneousMathematicalSymbolsB(): Set
    {
        return $this->block(0x2980, 0x29FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function supplementalMathematicalOperators(): Set
    {
        return $this->block(0x2A00, 0x2AFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function miscellaneousSymbolsAndArrows(): Set
    {
        return $this->block(0x2B00, 0x2BFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function glagolitic(): Set
    {
        return $this->block(0x2C00, 0x2C5F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function latinExtendedC(): Set
    {
        return $this->block(0x2C60, 0x2C7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function coptic(): Set
    {
        return $this->block(0x2C80, 0x2CFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function georgianSupplement(): Set
    {
        return $this->block(0x2D00, 0x2D2F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tifinagh(): Set
    {
        return $this->block(0x2D30, 0x2D7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ethiopicExtended(): Set
    {
        return $this->block(0x2D80, 0x2DDF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cyrillicExtendedA(): Set
    {
        return $this->block(0x2DE0, 0x2DFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function supplementalPunctuation(): Set
    {
        return $this->block(0x2E00, 0x2E7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkRadicalsSupplement(): Set
    {
        return $this->block(0x2E80, 0x2EFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kangxiRadicals(): Set
    {
        return $this->block(0x2F00, 0x2FDF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ideographicDescriptionCharacters(): Set
    {
        return $this->block(0x2FF0, 0x2FFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkSymbolsAndPunctuation(): Set
    {
        return $this->block(0x3000, 0x303F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hiragana(): Set
    {
        return $this->block(0x3040, 0x309F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function katakana(): Set
    {
        return $this->block(0x30A0, 0x30FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function bopomofo(): Set
    {
        return $this->block(0x3100, 0x312F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hangulCompatibilityJamo(): Set
    {
        return $this->block(0x3130, 0x318F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kanbun(): Set
    {
        return $this->block(0x3190, 0x319F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function bopomofoExtended(): Set
    {
        return $this->block(0x31A0, 0x31BF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkStrokes(): Set
    {
        return $this->block(0x31C0, 0x31EF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function katakanaPhoneticExtensions(): Set
    {
        return $this->block(0x31F0, 0x31FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function enclosedCJKLettersAndMonths(): Set
    {
        return $this->block(0x3200, 0x32FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkCompatibility(): Set
    {
        return $this->block(0x3300, 0x33FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkUnifiedIdeographsExtensionA(): Set
    {
        return $this->block(0x3400, 0x4DBF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function yijingHexagramSymbols(): Set
    {
        return $this->block(0x4DC0, 0x4DFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkUnifiedIdeographs(): Set
    {
        return $this->block(0x4E00, 0x9FFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function yiSyllables(): Set
    {
        return $this->block(0xA000, 0xA48F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function yiRadicals(): Set
    {
        return $this->block(0xA490, 0xA4CF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function lisu(): Set
    {
        return $this->block(0xA4D0, 0xA4FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function vai(): Set
    {
        return $this->block(0xA500, 0xA63F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cyrillicExtendedB(): Set
    {
        return $this->block(0xA640, 0xA69F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function bamum(): Set
    {
        return $this->block(0xA6A0, 0xA6FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function modifierToneLetters(): Set
    {
        return $this->block(0xA700, 0xA71F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function latinExtendedD(): Set
    {
        return $this->block(0xA720, 0xA7FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function sylotiNagri(): Set
    {
        return $this->block(0xA800, 0xA82F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function commonIndicNumberForms(): Set
    {
        return $this->block(0xA830, 0xA83F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function phagsPa(): Set
    {
        return $this->block(0xA840, 0xA87F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function saurashtra(): Set
    {
        return $this->block(0xA880, 0xA8DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function devanagariExtended(): Set
    {
        return $this->block(0xA8E0, 0xA8FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kayahLi(): Set
    {
        return $this->block(0xA900, 0xA92F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function rejang(): Set
    {
        return $this->block(0xA930, 0xA95F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hangulJamoExtendedA(): Set
    {
        return $this->block(0xA960, 0xA97F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function javanese(): Set
    {
        return $this->block(0xA980, 0xA9DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function myanmarExtendedB(): Set
    {
        return $this->block(0xA9E0, 0xA9FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cham(): Set
    {
        return $this->block(0xAA00, 0xAA5F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function myanmarExtendedA(): Set
    {
        return $this->block(0xAA60, 0xAA7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function taiViet(): Set
    {
        return $this->block(0xAA80, 0xAADF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function meeteiMayekExtensions(): Set
    {
        return $this->block(0xAAE0, 0xAAFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ethiopicExtendedA(): Set
    {
        return $this->block(0xAB00, 0xAB2F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function latinExtendedE(): Set
    {
        return $this->block(0xAB30, 0xAB6F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cherokeeSupplement(): Set
    {
        return $this->block(0xAB70, 0xABBF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function meeteiMayek(): Set
    {
        return $this->block(0xABC0, 0xABFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hangulSyllables(): Set
    {
        return $this->block(0xAC00, 0xD7AF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hangulJamoExtendedB(): Set
    {
        return $this->block(0xB7B0, 0xD7FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkCompatibilityIdeographs(): Set
    {
        return $this->block(0xF900, 0xFAFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function alphabeticPresentationForms(): Set
    {
        return $this->block(0xFB00, 0xFB4F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function arabicPresentationFormsA(): Set
    {
        return $this->block(0xFB50, 0xFDFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function variationSelectors(): Set
    {
        return $this->block(0xFE00, 0xFE0F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function verticalForms(): Set
    {
        return $this->block(0xFE10, 0xFE1F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function combiningHalfMarks(): Set
    {
        return $this->block(0xFE20, 0xFE2F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkCompatibilityForms(): Set
    {
        return $this->block(0xFE30, 0xFE4F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function smallFormVariants(): Set
    {
        return $this->block(0xFE50, 0xFE6F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function arabicPresentationFormsB(): Set
    {
        return $this->block(0xFE70, 0xFEFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function halfwidthAndFullwidthForms(): Set
    {
        return $this->block(0xFF00, 0xFFEF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function specials(): Set
    {
        return $this->block(0xFFF0, 0xFFFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function linearBSyllabary(): Set
    {
        return $this->block(0x10000, 0x1007F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function linearBIdeograms(): Set
    {
        return $this->block(0x10080, 0x100FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function aeganNumbers(): Set
    {
        return $this->block(0x10100, 0x1013F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ancientGreekNumbers(): Set
    {
        return $this->block(0x10140, 0x1018F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ancientSymbols(): Set
    {
        return $this->block(0x10190, 0x101CF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function phaistosDisc(): Set
    {
        return $this->block(0x101D0, 0x101FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function lycian(): Set
    {
        return $this->block(0x10280, 0x1029F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function carian(): Set
    {
        return $this->block(0x102A0, 0x102DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function copticEpactNumbers(): Set
    {
        return $this->block(0x102E0, 0x102FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oldItalic(): Set
    {
        return $this->block(0x10300, 0x1032F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function gothic(): Set
    {
        return $this->block(0x10330, 0x1034F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oldPermic(): Set
    {
        return $this->block(0x10350, 0x1037F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ugaritic(): Set
    {
        return $this->block(0x10380, 0x1039F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oldPersian(): Set
    {
        return $this->block(0x103A0, 0x103DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function deseret(): Set
    {
        return $this->block(0x1040, 0x1044F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function shavian(): Set
    {
        return $this->block(0x10450, 0x1047F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function osmanya(): Set
    {
        return $this->block(0x10480, 0x104AF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function osage(): Set
    {
        return $this->block(0x104B0, 0x104FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function elbasan(): Set
    {
        return $this->block(0x10500, 0x1052F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function caucasianAlbanian(): Set
    {
        return $this->block(0x10530, 0x1056F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function linearA(): Set
    {
        return $this->block(0x10600, 0x1077F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cypriotSyllabary(): Set
    {
        return $this->block(0x10800, 0x1083F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function imperialAramaic(): Set
    {
        return $this->block(0x10840, 0x1085F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function palmyrene(): Set
    {
        return $this->block(0x10860, 0x1087F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function nabataean(): Set
    {
        return $this->block(0x10880, 0x108AF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function hatran(): Set
    {
        return $this->block(0x108E0, 0x108FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function phoenician(): Set
    {
        return $this->block(0x10900, 0x1091F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function lydian(): Set
    {
        return $this->block(0x10920, 0x1093F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function meroiticHieroglyphs(): Set
    {
        return $this->block(0x10980, 0x1099F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function meroiticCursive(): Set
    {
        return $this->block(0x109A0, 0x109FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kharoshthi(): Set
    {
        return $this->block(0x10A00, 0x10A5F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oldSouthArabian(): Set
    {
        return $this->block(0x10A60, 0x10A7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oldNorthArabian(): Set
    {
        return $this->block(0x10A80, 0x10A9F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function manichaean(): Set
    {
        return $this->block(0x10AC0, 0x10AFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function avestan(): Set
    {
        return $this->block(0x10B00, 0x10B3F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function inscriptionalParthian(): Set
    {
        return $this->block(0x10B40, 0x10B5F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function inscriptionalPahlavi(): Set
    {
        return $this->block(0x10B60, 0x10B7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function psalterPahlavi(): Set
    {
        return $this->block(0x10B80, 0x10BAF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oldTurkic(): Set
    {
        return $this->block(0x10C00, 0x10C4F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function oldHungarian(): Set
    {
        return $this->block(0x10C80, 0x10CFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function rumiNumeralSymbols(): Set
    {
        return $this->block(0x10E60, 0x10E7F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function brahmi(): Set
    {
        return $this->block(0x11000, 0x1107F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kaithi(): Set
    {
        return $this->block(0x11080, 0x110CF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function soraSompeg(): Set
    {
        return $this->block(0x110D0, 0x110FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function chakma(): Set
    {
        return $this->block(0x11100, 0x1114F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mahajani(): Set
    {
        return $this->block(0x11150, 0x1117F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function sharada(): Set
    {
        return $this->block(0x11180, 0x111DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function sinhalaArchaicNumbers(): Set
    {
        return $this->block(0x111E0, 0x111FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function khojki(): Set
    {
        return $this->block(0x11200, 0x1124F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function multani(): Set
    {
        return $this->block(0x11280, 0x112AF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function khudawadi(): Set
    {
        return $this->block(0x112B0, 0x112FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function grantha(): Set
    {
        return $this->block(0x11300, 0x1137F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function newa(): Set
    {
        return $this->block(0x11400, 0x1147F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tirhuta(): Set
    {
        return $this->block(0x11480, 0x114DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function siddham(): Set
    {
        return $this->block(0x11580, 0x115FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function modi(): Set
    {
        return $this->block(0x11600, 0x1165F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mongolianSupplement(): Set
    {
        return $this->block(0x11660, 0x1167F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function takri(): Set
    {
        return $this->block(0x11680, 0x116CF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ahom(): Set
    {
        return $this->block(0x11700, 0x1173F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function warangCiti(): Set
    {
        return $this->block(0x118A0, 0x118FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function zanabazarSquare(): Set
    {
        return $this->block(0x11A00, 0x11A4F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function soyombo(): Set
    {
        return $this->block(0x11A50, 0x11AAF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function pauCinHau(): Set
    {
        return $this->block(0x11AC0, 0x11AFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function bhaiksuki(): Set
    {
        return $this->block(0x11C00, 0x11C6F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function marchen(): Set
    {
        return $this->block(0x11C70, 0x11CBF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function masaramGondi(): Set
    {
        return $this->block(0x11D00, 0x11D5F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cuneiform(): Set
    {
        return $this->block(0x12000, 0x123FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cuneiformNumbersAndPunctuation(): Set
    {
        return $this->block(0x12400, 0x1247F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function earlyDynasticCuneiform(): Set
    {
        return $this->block(0x12480, 0x1254F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function egyptianHieroglyphs(): Set
    {
        return $this->block(0x13000, 0x1342F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function anatolianHieroglyphs(): Set
    {
        return $this->block(0x14400, 0x1467F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function bamumSupplement(): Set
    {
        return $this->block(0x16800, 0x16A3F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mro(): Set
    {
        return $this->block(0x16A40, 0x16A6F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function bassaVah(): Set
    {
        return $this->block(0x16AD0, 0x16AFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function pahawhHmong(): Set
    {
        return $this->block(0x16B00, 0x16B8F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function miao(): Set
    {
        return $this->block(0x16F00, 0x16F9F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ideographicSymbolsAndPunctuation(): Set
    {
        return $this->block(0x16FE0, 0x16FFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tangut(): Set
    {
        return $this->block(0x17000, 0x187FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tangutComponents(): Set
    {
        return $this->block(0x18800, 0x18AFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kanaSupplement(): Set
    {
        return $this->block(0x1B000, 0x1B0FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function kanaExtendedA(): Set
    {
        return $this->block(0x1B100, 0x1B12F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function nushu(): Set
    {
        return $this->block(0x1B170, 0x1B2FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function duployan(): Set
    {
        return $this->block(0x1BC00, 0x1BC9F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function shorthandFormatControls(): Set
    {
        return $this->block(0x1BCA0, 0x1BCAF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function byzantineMusicalSymbols(): Set
    {
        return $this->block(0x1D000, 0x1D0FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function musicalSymbols(): Set
    {
        return $this->block(0x1D100, 0x1D1FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ancientGreekMusicalNotation(): Set
    {
        return $this->block(0x1D200, 0x1D24F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function taiXuanJingSymbols(): Set
    {
        return $this->block(0x1D300, 0x1D35F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function countingRodNumerals(): Set
    {
        return $this->block(0x1D360, 0x1D37F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mathematicalAlphanumericSymbols(): Set
    {
        return $this->block(0x1D400, 0x1D7FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function suttonSignWriting(): Set
    {
        return $this->block(0x1D800, 0x1DAAF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function glagoliticSupplement(): Set
    {
        return $this->block(0x1E000, 0x1E02F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mendeKikakui(): Set
    {
        return $this->block(0x1E800, 0x1E8DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function adlam(): Set
    {
        return $this->block(0x1E900, 0x1E95F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function arabicMathematicalAlphabeticSymbols(): Set
    {
        return $this->block(0x1EE00, 0x1EEFF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function mahjongTiles(): Set
    {
        return $this->block(0x1F000, 0x1F02F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function dominoTiles(): Set
    {
        return $this->block(0x1F030, 0x1F09F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function playingCards(): Set
    {
        return $this->block(0x1F0A0, 0x1F0FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function enclosedAlphanumericSupplement(): Set
    {
        return $this->block(0x1F100, 0x1F1FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function enclosedIdeopgraphicSupplement(): Set
    {
        return $this->block(0x1F200, 0x1F2FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function miscellaneousSymbolsAndPictographs(): Set
    {
        return $this->block(0x1F300, 0x1F5FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function emoticons(): Set
    {
        return $this->block(0x1F600, 0x1F64F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function ornamentalDingbats(): Set
    {
        return $this->block(0x1F650, 0x1F67F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function transportAndMapSymbols(): Set
    {
        return $this->block(0x1F680, 0x1F6FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function alchemicalSymbols(): Set
    {
        return $this->block(0x1F700, 0x1F77F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function geometricShapesExtended(): Set
    {
        return $this->block(0x1F780, 0x1F7FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function supplementalArrowsC(): Set
    {
        return $this->block(0x1F800, 0x1F8FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function supplementalSymbolsAndPictographs(): Set
    {
        return $this->block(0x1F900, 0x1F9FF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkUnifiedIdeographsExtensionB(): Set
    {
        return $this->block(0x20000, 0x2A6DF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkUnifiedIdeographsExtensionC(): Set
    {
        return $this->block(0x2A700, 0x2B73F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkUnifiedIdeographsExtensionD(): Set
    {
        return $this->block(0x2B740, 0x2B81F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkUnifiedIdeographsExtensionE(): Set
    {
        return $this->block(0x2B820, 0x2CEAF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkUnifiedIdeographsExtensionF(): Set
    {
        return $this->block(0x2CEB0, 0x2EBEF);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function cjkCompatibilityIdeographsSupplement(): Set
    {
        return $this->block(0x2F800, 0x2FA1F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function tags(): Set
    {
        return $this->block(0xE0000, 0xE007F);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    public function variationSelectorsSupplement(): Set
    {
        return $this->block(0xE0100, 0xE01EF);
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<string>
     */
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(string): bool $predicate
     *
     * @return Set<string>
     */
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(string): V $map
     *
     * @return Set<V>
     */
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(string): (Set<V>|Provider<V>) $map
     *
     * @return Set<V>
     */
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return Set::strings()
            ->madeOf($this->char())
            ->toSet();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<string>
     */
    private function block(int $min, int $max): Set
    {
        /** @var Set<string> */
        return Set::integers()
            ->between($min, $max)
            ->map(\IntlChar::chr(...))
            ->filter(static fn($char) => \is_string($char));
    }
}
