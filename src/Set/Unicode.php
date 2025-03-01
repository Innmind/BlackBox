<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @see https://unicode-table.com/en/blocks/
 */
final class Unicode
{
    /**
     * @psalm-pure
     */
    public static function strings(): MadeOf
    {
        return Strings::madeOf(self::any());
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function any(): Set
    {
        $methods = \get_class_methods(self::class);
        $methods = \array_filter(
            $methods,
            static fn(string $method): bool => !\in_array($method, ['any', 'between', 'strings', 'lengthBetween'], true),
        );
        /**
         * @psalm-suppress MixedReturnStatement
         * @psalm-suppress MixedInferredReturnType
         * @var non-empty-list<Set<string>>
         */
        $sets = \array_map(
            static fn(string $method): Set => self::{$method}(),
            $methods,
        );

        return Set::of(Either::any(...$sets));
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function controlCharater(): Set
    {
        return self::between(0x0000, 0X001F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function basicLatin(): Set
    {
        return self::between(0x0020, 0x007F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latin1Supplement(): Set
    {
        return self::between(0x0080, 0x00FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedA(): Set
    {
        return self::between(0x0100, 0x017F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedB(): Set
    {
        return self::between(0x0180, 0x024F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ipaExtensions(): Set
    {
        return self::between(0x0250, 0x02AF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function spacingModifierLetters(): Set
    {
        return self::between(0x02B0, 0x02FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarks(): Set
    {
        return self::between(0x0300, 0x036F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function greekAndCoptic(): Set
    {
        return self::between(0x0370, 0x03FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillic(): Set
    {
        return self::between(0x0400, 0x04FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicSupplement(): Set
    {
        return self::between(0x0500, 0x052F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function armenian(): Set
    {
        return self::between(0x0530, 0x058F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hebrew(): Set
    {
        return self::between(0x0590, 0x05FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabic(): Set
    {
        return self::between(0x0600, 0x06FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function syriac(): Set
    {
        return self::between(0x0700, 0x074F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicSupplement(): Set
    {
        return self::between(0x0750, 0x077F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function thaana(): Set
    {
        return self::between(0x0780, 0x07BF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function nko(): Set
    {
        return self::between(0x07C0, 0x07FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function samaritan(): Set
    {
        return self::between(0x0800, 0x083F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mandaic(): Set
    {
        return self::between(0x0840, 0x085F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function syriacSupplement(): Set
    {
        return self::between(0x0860, 0x086F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicExtendedA(): Set
    {
        return self::between(0x08A0, 0x08FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function devanagari(): Set
    {
        return self::between(0x0900, 0x097F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bengali(): Set
    {
        return self::between(0x0980, 0x09FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function gurmukhi(): Set
    {
        return self::between(0x0A00, 0x0A7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function gujarati(): Set
    {
        return self::between(0x0A80, 0x0AFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oriya(): Set
    {
        return self::between(0x0B00, 0x0B7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tamil(): Set
    {
        return self::between(0x0B80, 0x0BFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function telugu(): Set
    {
        return self::between(0x0C00, 0x0C7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kannada(): Set
    {
        return self::between(0x0C80, 0x0CFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function malayalam(): Set
    {
        return self::between(0x0D00, 0x0D7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sinhala(): Set
    {
        return self::between(0x0D80, 0x0DFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function thai(): Set
    {
        return self::between(0x0E00, 0x0E7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lao(): Set
    {
        return self::between(0x0E80, 0x0EFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tibetan(): Set
    {
        return self::between(0x0F00, 0x0FFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function myanmar(): Set
    {
        return self::between(0x1000, 0x109F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function georgian(): Set
    {
        return self::between(0x10A0, 0x10FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulJamo(): Set
    {
        return self::between(0x1100, 0x11FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopic(): Set
    {
        return self::between(0x1200, 0x137F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopicSupplement(): Set
    {
        return self::between(0x1380, 0x139F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cherokee(): Set
    {
        return self::between(0x13A0, 0x13FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function unifiedCanadianAboriginalSyllabics(): Set
    {
        return self::between(0x1400, 0x167F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ogham(): Set
    {
        return self::between(0x1680, 0x169F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function runic(): Set
    {
        return self::between(0x16A0, 0x16FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tagalog(): Set
    {
        return self::between(0x1700, 0x171F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hanunoo(): Set
    {
        return self::between(0x1720, 0x173F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function buhid(): Set
    {
        return self::between(0x1740, 0x175F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tagbanwa(): Set
    {
        return self::between(0x1760, 0x177F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khmer(): Set
    {
        return self::between(0x1780, 0x17FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mongolian(): Set
    {
        return self::between(0x1800, 0x18AF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function unifiedCanadianAboriginalSyllabicsExtended(): Set
    {
        return self::between(0x18B0, 0x18FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function limbu(): Set
    {
        return self::between(0x1900, 0x194F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiLe(): Set
    {
        return self::between(0x1950, 0x197F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function newTaiLue(): Set
    {
        return self::between(0x1980, 0x19DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khmerSymbols(): Set
    {
        return self::between(0x19E0, 0x19FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function buginese(): Set
    {
        return self::between(0x1A00, 0x1A1F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiTham(): Set
    {
        return self::between(0x1A20, 0x1AAF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarksExtended(): Set
    {
        return self::between(0x1AB0, 0x1AFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function balinese(): Set
    {
        return self::between(0x1B00, 0x1B7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sundanese(): Set
    {
        return self::between(0x1B80, 0x1BBF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function batak(): Set
    {
        return self::between(0x1BC0, 0x1BFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lepcha(): Set
    {
        return self::between(0x1C00, 0x1C4F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function olChiki(): Set
    {
        return self::between(0x1C50, 0x1C7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicExtendedC(): Set
    {
        return self::between(0x1C80, 0x1C8F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sundaneseSupplement(): Set
    {
        return self::between(0x1CC0, 0x1CCF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function vedicExtensions(): Set
    {
        return self::between(0x1CD0, 0x1CFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phoneticExtensions(): Set
    {
        return self::between(0x1D00, 0x1D7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phoneticExtensionsSupplement(): Set
    {
        return self::between(0x1D80, 0x1D8F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarksSupplement(): Set
    {
        return self::between(0x1DC0, 0x1DFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedAdditional(): Set
    {
        return self::between(0x1E00, 0x1EFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function greekExtended(): Set
    {
        return self::between(0x1F00, 0x1FFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function generalPunctuation(): Set
    {
        return self::between(0x2000, 0x206F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function superscriptsAndSubscripts(): Set
    {
        return self::between(0x2070, 0x209F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function currencySymbols(): Set
    {
        return self::between(0x20A0, 0x20CF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarksForSymbols(): Set
    {
        return self::between(0x20D0, 0x20FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function letterlikeSymbols(): Set
    {
        return self::between(0x2100, 0x214F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function numberForms(): Set
    {
        return self::between(0x2150, 0x218F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arrows(): Set
    {
        return self::between(0x2190, 0x21FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mathematicalOperators(): Set
    {
        return self::between(0x2200, 0x22FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousTechnical(): Set
    {
        return self::between(0x2300, 0x23FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function controlPictures(): Set
    {
        return self::between(0x2400, 0x243F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function opticalCharacterRecognition(): Set
    {
        return self::between(0x2440, 0x245F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedAlphanumerics(): Set
    {
        return self::between(0x2460, 0x247F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function boxDrawing(): Set
    {
        return self::between(0x2500, 0x257F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function blockElements(): Set
    {
        return self::between(0x2580, 0x259F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function geometricShapes(): Set
    {
        return self::between(0x25A0, 0x25FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousSymbols(): Set
    {
        return self::between(0x2600, 0x26FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function dingbats(): Set
    {
        return self::between(0x270, 0x27BF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousMathematicalSymbolsA(): Set
    {
        return self::between(0x27C0, 0x27EF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalArrowsA(): Set
    {
        return self::between(0x27F0, 0x27FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function braillePatterns(): Set
    {
        return self::between(0x2800, 0x28FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalArrowsB(): Set
    {
        return self::between(0x2900, 0x297F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousMathematicalSymbolsB(): Set
    {
        return self::between(0x2980, 0x29FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalMathematicalOperators(): Set
    {
        return self::between(0x2A00, 0x2AFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousSymbolsAndArrows(): Set
    {
        return self::between(0x2B00, 0x2BFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function glagolitic(): Set
    {
        return self::between(0x2C00, 0x2C5F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedC(): Set
    {
        return self::between(0x2C60, 0x2C7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function coptic(): Set
    {
        return self::between(0x2C80, 0x2CFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function georgianSupplement(): Set
    {
        return self::between(0x2D00, 0x2D2F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tifinagh(): Set
    {
        return self::between(0x2D30, 0x2D7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopicExtended(): Set
    {
        return self::between(0x2D80, 0x2DDF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicExtendedA(): Set
    {
        return self::between(0x2DE0, 0x2DFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalPunctuation(): Set
    {
        return self::between(0x2E00, 0x2E7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkRadicalsSupplement(): Set
    {
        return self::between(0x2E80, 0x2EFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kangxiRadicals(): Set
    {
        return self::between(0x2F00, 0x2FDF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ideographicDescriptionCharacters(): Set
    {
        return self::between(0x2FF0, 0x2FFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkSymbolsAndPunctuation(): Set
    {
        return self::between(0x3000, 0x303F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hiragana(): Set
    {
        return self::between(0x3040, 0x309F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function katakana(): Set
    {
        return self::between(0x30A0, 0x30FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bopomofo(): Set
    {
        return self::between(0x3100, 0x312F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulCompatibilityJamo(): Set
    {
        return self::between(0x3130, 0x318F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kanbun(): Set
    {
        return self::between(0x3190, 0x319F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bopomofoExtended(): Set
    {
        return self::between(0x31A0, 0x31BF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkStrokes(): Set
    {
        return self::between(0x31C0, 0x31EF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function katakanaPhoneticExtensions(): Set
    {
        return self::between(0x31F0, 0x31FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedCJKLettersAndMonths(): Set
    {
        return self::between(0x3200, 0x32FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibility(): Set
    {
        return self::between(0x3300, 0x33FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionA(): Set
    {
        return self::between(0x3400, 0x4DBF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function yijingHexagramSymbols(): Set
    {
        return self::between(0x4DC0, 0x4DFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographs(): Set
    {
        return self::between(0x4E00, 0x9FFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function yiSyllables(): Set
    {
        return self::between(0xA000, 0xA48F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function yiRadicals(): Set
    {
        return self::between(0xA490, 0xA4CF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lisu(): Set
    {
        return self::between(0xA4D0, 0xA4FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function vai(): Set
    {
        return self::between(0xA500, 0xA63F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicExtendedB(): Set
    {
        return self::between(0xA640, 0xA69F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bamum(): Set
    {
        return self::between(0xA6A0, 0xA6FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function modifierToneLetters(): Set
    {
        return self::between(0xA700, 0xA71F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedD(): Set
    {
        return self::between(0xA720, 0xA7FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sylotiNagri(): Set
    {
        return self::between(0xA800, 0xA82F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function commonIndicNumberForms(): Set
    {
        return self::between(0xA830, 0xA83F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phagsPa(): Set
    {
        return self::between(0xA840, 0xA87F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function saurashtra(): Set
    {
        return self::between(0xA880, 0xA8DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function devanagariExtended(): Set
    {
        return self::between(0xA8E0, 0xA8FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kayahLi(): Set
    {
        return self::between(0xA900, 0xA92F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function rejang(): Set
    {
        return self::between(0xA930, 0xA95F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulJamoExtendedA(): Set
    {
        return self::between(0xA960, 0xA97F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function javanese(): Set
    {
        return self::between(0xA980, 0xA9DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function myanmarExtendedB(): Set
    {
        return self::between(0xA9E0, 0xA9FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cham(): Set
    {
        return self::between(0xAA00, 0xAA5F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function myanmarExtendedA(): Set
    {
        return self::between(0xAA60, 0xAA7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiViet(): Set
    {
        return self::between(0xAA80, 0xAADF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meeteiMayekExtensions(): Set
    {
        return self::between(0xAAE0, 0xAAFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopicExtendedA(): Set
    {
        return self::between(0xAB00, 0xAB2F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedE(): Set
    {
        return self::between(0xAB30, 0xAB6F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cherokeeSupplement(): Set
    {
        return self::between(0xAB70, 0xABBF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meeteiMayek(): Set
    {
        return self::between(0xABC0, 0xABFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulSyllables(): Set
    {
        return self::between(0xAC00, 0xD7AF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulJamoExtendedB(): Set
    {
        return self::between(0xB7B0, 0xD7FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibilityIdeographs(): Set
    {
        return self::between(0xF900, 0xFAFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function alphabeticPresentationForms(): Set
    {
        return self::between(0xFB00, 0xFB4F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicPresentationFormsA(): Set
    {
        return self::between(0xFB50, 0xFDFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function variationSelectors(): Set
    {
        return self::between(0xFE00, 0xFE0F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function verticalForms(): Set
    {
        return self::between(0xFE10, 0xFE1F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningHalfMarks(): Set
    {
        return self::between(0xFE20, 0xFE2F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibilityForms(): Set
    {
        return self::between(0xFE30, 0xFE4F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function smallFormVariants(): Set
    {
        return self::between(0xFE50, 0xFE6F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicPresentationFormsB(): Set
    {
        return self::between(0xFE70, 0xFEFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function halfwidthAndFullwidthForms(): Set
    {
        return self::between(0xFF00, 0xFFEF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function specials(): Set
    {
        return self::between(0xFFF0, 0xFFFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function linearBSyllabary(): Set
    {
        return self::between(0x10000, 0x1007F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function linearBIdeograms(): Set
    {
        return self::between(0x10080, 0x100FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function aeganNumbers(): Set
    {
        return self::between(0x10100, 0x1013F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ancientGreekNumbers(): Set
    {
        return self::between(0x10140, 0x1018F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ancientSymbols(): Set
    {
        return self::between(0x10190, 0x101CF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phaistosDisc(): Set
    {
        return self::between(0x101D0, 0x101FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lycian(): Set
    {
        return self::between(0x10280, 0x1029F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function carian(): Set
    {
        return self::between(0x102A0, 0x102DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function copticEpactNumbers(): Set
    {
        return self::between(0x102E0, 0x102FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldItalic(): Set
    {
        return self::between(0x10300, 0x1032F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function gothic(): Set
    {
        return self::between(0x10330, 0x1034F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldPermic(): Set
    {
        return self::between(0x10350, 0x1037F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ugaritic(): Set
    {
        return self::between(0x10380, 0x1039F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldPersian(): Set
    {
        return self::between(0x103A0, 0x103DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function deseret(): Set
    {
        return self::between(0x1040, 0x1044F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function shavian(): Set
    {
        return self::between(0x10450, 0x1047F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function osmanya(): Set
    {
        return self::between(0x10480, 0x104AF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function osage(): Set
    {
        return self::between(0x104B0, 0x104FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function elbasan(): Set
    {
        return self::between(0x10500, 0x1052F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function caucasianAlbanian(): Set
    {
        return self::between(0x10530, 0x1056F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function linearA(): Set
    {
        return self::between(0x10600, 0x1077F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cypriotSyllabary(): Set
    {
        return self::between(0x10800, 0x1083F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function imperialAramaic(): Set
    {
        return self::between(0x10840, 0x1085F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function palmyrene(): Set
    {
        return self::between(0x10860, 0x1087F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function nabataean(): Set
    {
        return self::between(0x10880, 0x108AF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hatran(): Set
    {
        return self::between(0x108E0, 0x108FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phoenician(): Set
    {
        return self::between(0x10900, 0x1091F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lydian(): Set
    {
        return self::between(0x10920, 0x1093F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meroiticHieroglyphs(): Set
    {
        return self::between(0x10980, 0x1099F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meroiticCursive(): Set
    {
        return self::between(0x109A0, 0x109FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kharoshthi(): Set
    {
        return self::between(0x10A00, 0x10A5F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldSouthArabian(): Set
    {
        return self::between(0x10A60, 0x10A7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldNorthArabian(): Set
    {
        return self::between(0x10A80, 0x10A9F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function manichaean(): Set
    {
        return self::between(0x10AC0, 0x10AFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function avestan(): Set
    {
        return self::between(0x10B00, 0x10B3F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function inscriptionalParthian(): Set
    {
        return self::between(0x10B40, 0x10B5F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function inscriptionalPahlavi(): Set
    {
        return self::between(0x10B60, 0x10B7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function psalterPahlavi(): Set
    {
        return self::between(0x10B80, 0x10BAF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldTurkic(): Set
    {
        return self::between(0x10C00, 0x10C4F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldHungarian(): Set
    {
        return self::between(0x10C80, 0x10CFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function rumiNumeralSymbols(): Set
    {
        return self::between(0x10E60, 0x10E7F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function brahmi(): Set
    {
        return self::between(0x11000, 0x1107F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kaithi(): Set
    {
        return self::between(0x11080, 0x110CF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function soraSompeg(): Set
    {
        return self::between(0x110D0, 0x110FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function chakma(): Set
    {
        return self::between(0x11100, 0x1114F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mahajani(): Set
    {
        return self::between(0x11150, 0x1117F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sharada(): Set
    {
        return self::between(0x11180, 0x111DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sinhalaArchaicNumbers(): Set
    {
        return self::between(0x111E0, 0x111FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khojki(): Set
    {
        return self::between(0x11200, 0x1124F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function multani(): Set
    {
        return self::between(0x11280, 0x112AF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khudawadi(): Set
    {
        return self::between(0x112B0, 0x112FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function grantha(): Set
    {
        return self::between(0x11300, 0x1137F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function newa(): Set
    {
        return self::between(0x11400, 0x1147F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tirhuta(): Set
    {
        return self::between(0x11480, 0x114DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function siddham(): Set
    {
        return self::between(0x11580, 0x115FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function modi(): Set
    {
        return self::between(0x11600, 0x1165F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mongolianSupplement(): Set
    {
        return self::between(0x11660, 0x1167F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function takri(): Set
    {
        return self::between(0x11680, 0x116CF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ahom(): Set
    {
        return self::between(0x11700, 0x1173F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function warangCiti(): Set
    {
        return self::between(0x118A0, 0x118FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function zanabazarSquare(): Set
    {
        return self::between(0x11A00, 0x11A4F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function soyombo(): Set
    {
        return self::between(0x11A50, 0x11AAF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function pauCinHau(): Set
    {
        return self::between(0x11AC0, 0x11AFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bhaiksuki(): Set
    {
        return self::between(0x11C00, 0x11C6F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function marchen(): Set
    {
        return self::between(0x11C70, 0x11CBF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function masaramGondi(): Set
    {
        return self::between(0x11D00, 0x11D5F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cuneiform(): Set
    {
        return self::between(0x12000, 0x123FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cuneiformNumbersAndPunctuation(): Set
    {
        return self::between(0x12400, 0x1247F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function earlyDynasticCuneiform(): Set
    {
        return self::between(0x12480, 0x1254F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function egyptianHieroglyphs(): Set
    {
        return self::between(0x13000, 0x1342F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function anatolianHieroglyphs(): Set
    {
        return self::between(0x14400, 0x1467F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bamumSupplement(): Set
    {
        return self::between(0x16800, 0x16A3F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mro(): Set
    {
        return self::between(0x16A40, 0x16A6F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bassaVah(): Set
    {
        return self::between(0x16AD0, 0x16AFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function pahawhHmong(): Set
    {
        return self::between(0x16B00, 0x16B8F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miao(): Set
    {
        return self::between(0x16F00, 0x16F9F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ideographicSymbolsAndPunctuation(): Set
    {
        return self::between(0x16FE0, 0x16FFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tangut(): Set
    {
        return self::between(0x17000, 0x187FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tangutComponents(): Set
    {
        return self::between(0x18800, 0x18AFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kanaSupplement(): Set
    {
        return self::between(0x1B000, 0x1B0FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kanaExtendedA(): Set
    {
        return self::between(0x1B100, 0x1B12F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function nushu(): Set
    {
        return self::between(0x1B170, 0x1B2FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function duployan(): Set
    {
        return self::between(0x1BC00, 0x1BC9F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function shorthandFormatControls(): Set
    {
        return self::between(0x1BCA0, 0x1BCAF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function byzantineMusicalSymbols(): Set
    {
        return self::between(0x1D000, 0x1D0FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function musicalSymbols(): Set
    {
        return self::between(0x1D100, 0x1D1FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ancientGreekMusicalNotation(): Set
    {
        return self::between(0x1D200, 0x1D24F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiXuanJingSymbols(): Set
    {
        return self::between(0x1D300, 0x1D35F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function countingRodNumerals(): Set
    {
        return self::between(0x1D360, 0x1D37F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mathematicalAlphanumericSymbols(): Set
    {
        return self::between(0x1D400, 0x1D7FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function suttonSignWriting(): Set
    {
        return self::between(0x1D800, 0x1DAAF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function glagoliticSupplement(): Set
    {
        return self::between(0x1E000, 0x1E02F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mendeKikakui(): Set
    {
        return self::between(0x1E800, 0x1E8DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function adlam(): Set
    {
        return self::between(0x1E900, 0x1E95F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicMathematicalAlphabeticSymbols(): Set
    {
        return self::between(0x1EE00, 0x1EEFF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mahjongTiles(): Set
    {
        return self::between(0x1F000, 0x1F02F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function dominoTiles(): Set
    {
        return self::between(0x1F030, 0x1F09F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function playingCards(): Set
    {
        return self::between(0x1F0A0, 0x1F0FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedAlphanumericSupplement(): Set
    {
        return self::between(0x1F100, 0x1F1FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedIdeopgraphicSupplement(): Set
    {
        return self::between(0x1F200, 0x1F2FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousSymbolsAndPictographs(): Set
    {
        return self::between(0x1F300, 0x1F5FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function emoticons(): Set
    {
        return self::between(0x1F600, 0x1F64F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ornamentalDingbats(): Set
    {
        return self::between(0x1F650, 0x1F67F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function transportAndMapSymbols(): Set
    {
        return self::between(0x1F680, 0x1F6FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function alchemicalSymbols(): Set
    {
        return self::between(0x1F700, 0x1F77F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function geometricShapesExtended(): Set
    {
        return self::between(0x1F780, 0x1F7FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalArrowsC(): Set
    {
        return self::between(0x1F800, 0x1F8FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalSymbolsAndPictographs(): Set
    {
        return self::between(0x1F900, 0x1F9FF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionB(): Set
    {
        return self::between(0x20000, 0x2A6DF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionC(): Set
    {
        return self::between(0x2A700, 0x2B73F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionD(): Set
    {
        return self::between(0x2B740, 0x2B81F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionE(): Set
    {
        return self::between(0x2B820, 0x2CEAF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionF(): Set
    {
        return self::between(0x2CEB0, 0x2EBEF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibilityIdeographsSupplement(): Set
    {
        return self::between(0x2F800, 0x2FA1F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tags(): Set
    {
        return self::between(0xE0000, 0xE007F);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function variationSelectorsSupplement(): Set
    {
        return self::between(0xE0100, 0xE01EF);
    }

    /**
     * @psalm-pure
     *
     * @return Set<string>
     */
    private static function between(int $min, int $max): Set
    {
        /** @var Set<string> */
        return Integers::between($min, $max)
            ->map(\IntlChar::chr(...))
            ->filter(static fn($char) => \is_string($char));
    }
}
