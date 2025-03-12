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
     * @deprecated Use Set::strings()->unicode() instead
     * @psalm-pure
     */
    public static function strings(): MadeOf
    {
        return Set::strings()->madeOf(
            Set::strings()->unicode()->char(),
        );
    }

    /**
     * @deprecated Use Set::strings()->unicode()->char() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function any(): Set
    {
        return Set::strings()->unicode()->char();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->controlCharater() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function controlCharater(): Set
    {
        return Set::strings()
            ->unicode()
            ->controlCharater();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->basicLatin() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function basicLatin(): Set
    {
        return Set::strings()
            ->unicode()
            ->basicLatin();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->latin1Supplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latin1Supplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->latin1Supplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->latinExtendedA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedA(): Set
    {
        return Set::strings()
            ->unicode()
            ->latinExtendedA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->latinExtendedB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedB(): Set
    {
        return Set::strings()
            ->unicode()
            ->latinExtendedB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ipaExtensions() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ipaExtensions(): Set
    {
        return Set::strings()
            ->unicode()
            ->ipaExtensions();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->spacingModifierLetters() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function spacingModifierLetters(): Set
    {
        return Set::strings()
            ->unicode()
            ->spacingModifierLetters();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->combiningDiacriticalMarks() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarks(): Set
    {
        return Set::strings()
            ->unicode()
            ->combiningDiacriticalMarks();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->greekAndCoptic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function greekAndCoptic(): Set
    {
        return Set::strings()
            ->unicode()
            ->greekAndCoptic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cyrillic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillic(): Set
    {
        return Set::strings()
            ->unicode()
            ->cyrillic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cyrillicSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->cyrillicSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->armenian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function armenian(): Set
    {
        return Set::strings()
            ->unicode()
            ->armenian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hebrew() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hebrew(): Set
    {
        return Set::strings()
            ->unicode()
            ->hebrew();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->arabic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabic(): Set
    {
        return Set::strings()
            ->unicode()
            ->arabic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->syriac() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function syriac(): Set
    {
        return Set::strings()
            ->unicode()
            ->syriac();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->arabicSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->arabicSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->thaana() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function thaana(): Set
    {
        return Set::strings()
            ->unicode()
            ->thaana();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->nko() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function nko(): Set
    {
        return Set::strings()
            ->unicode()
            ->nko();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->samaritan() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function samaritan(): Set
    {
        return Set::strings()
            ->unicode()
            ->samaritan();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mandaic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mandaic(): Set
    {
        return Set::strings()
            ->unicode()
            ->mandaic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->syriacSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function syriacSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->syriacSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->arabicExtendedA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicExtendedA(): Set
    {
        return Set::strings()
            ->unicode()
            ->arabicExtendedA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->devanagari() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function devanagari(): Set
    {
        return Set::strings()
            ->unicode()
            ->devanagari();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->bengali() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bengali(): Set
    {
        return Set::strings()
            ->unicode()
            ->bengali();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->gurmukhi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function gurmukhi(): Set
    {
        return Set::strings()
            ->unicode()
            ->gurmukhi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->gujarati() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function gujarati(): Set
    {
        return Set::strings()
            ->unicode()
            ->gujarati();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oriya() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oriya(): Set
    {
        return Set::strings()
            ->unicode()
            ->oriya();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tamil() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tamil(): Set
    {
        return Set::strings()
            ->unicode()
            ->tamil();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->telugu() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function telugu(): Set
    {
        return Set::strings()
            ->unicode()
            ->telugu();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kannada() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kannada(): Set
    {
        return Set::strings()
            ->unicode()
            ->kannada();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->malayalam() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function malayalam(): Set
    {
        return Set::strings()
            ->unicode()
            ->malayalam();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->sinhala() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sinhala(): Set
    {
        return Set::strings()
            ->unicode()
            ->sinhala();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->thai() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function thai(): Set
    {
        return Set::strings()
            ->unicode()
            ->thai();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->lao() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lao(): Set
    {
        return Set::strings()
            ->unicode()
            ->lao();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tibetan() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tibetan(): Set
    {
        return Set::strings()
            ->unicode()
            ->tibetan();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->myanmar() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function myanmar(): Set
    {
        return Set::strings()
            ->unicode()
            ->myanmar();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->georgian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function georgian(): Set
    {
        return Set::strings()
            ->unicode()
            ->georgian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hangulJamo() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulJamo(): Set
    {
        return Set::strings()
            ->unicode()
            ->hangulJamo();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ethiopic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopic(): Set
    {
        return Set::strings()
            ->unicode()
            ->ethiopic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ethiopicSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopicSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->ethiopicSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cherokee() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cherokee(): Set
    {
        return Set::strings()
            ->unicode()
            ->cherokee();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->unifiedCanadianAboriginalSyllabics() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function unifiedCanadianAboriginalSyllabics(): Set
    {
        return Set::strings()
            ->unicode()
            ->unifiedCanadianAboriginalSyllabics();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ogham() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ogham(): Set
    {
        return Set::strings()
            ->unicode()
            ->ogham();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->runic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function runic(): Set
    {
        return Set::strings()
            ->unicode()
            ->runic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tagalog() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tagalog(): Set
    {
        return Set::strings()
            ->unicode()
            ->tagalog();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hanunoo() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hanunoo(): Set
    {
        return Set::strings()
            ->unicode()
            ->hanunoo();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->buhid() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function buhid(): Set
    {
        return Set::strings()
            ->unicode()
            ->buhid();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tagbanwa() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tagbanwa(): Set
    {
        return Set::strings()
            ->unicode()
            ->tagbanwa();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->khmer() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khmer(): Set
    {
        return Set::strings()
            ->unicode()
            ->khmer();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mongolian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mongolian(): Set
    {
        return Set::strings()
            ->unicode()
            ->mongolian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->unifiedCanadianAboriginalSyllabicsExtended() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function unifiedCanadianAboriginalSyllabicsExtended(): Set
    {
        return Set::strings()
            ->unicode()
            ->unifiedCanadianAboriginalSyllabicsExtended();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->limbu() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function limbu(): Set
    {
        return Set::strings()
            ->unicode()
            ->limbu();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->taiLe() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiLe(): Set
    {
        return Set::strings()
            ->unicode()
            ->taiLe();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->newTaiLue() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function newTaiLue(): Set
    {
        return Set::strings()
            ->unicode()
            ->newTaiLue();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->khmerSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khmerSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->khmerSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->buginese() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function buginese(): Set
    {
        return Set::strings()
            ->unicode()
            ->buginese();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->taiTham() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiTham(): Set
    {
        return Set::strings()
            ->unicode()
            ->taiTham();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->combiningDiacriticalMarksExtended() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarksExtended(): Set
    {
        return Set::strings()
            ->unicode()
            ->combiningDiacriticalMarksExtended();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->balinese() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function balinese(): Set
    {
        return Set::strings()
            ->unicode()
            ->balinese();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->sundanese() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sundanese(): Set
    {
        return Set::strings()
            ->unicode()
            ->sundanese();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->batak() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function batak(): Set
    {
        return Set::strings()
            ->unicode()
            ->batak();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->lepcha() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lepcha(): Set
    {
        return Set::strings()
            ->unicode()
            ->lepcha();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->olChiki() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function olChiki(): Set
    {
        return Set::strings()
            ->unicode()
            ->olChiki();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cyrillicExtendedC() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicExtendedC(): Set
    {
        return Set::strings()
            ->unicode()
            ->cyrillicExtendedC();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->sundaneseSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sundaneseSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->sundaneseSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->vedicExtensions() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function vedicExtensions(): Set
    {
        return Set::strings()
            ->unicode()
            ->vedicExtensions();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->phoneticExtensions() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phoneticExtensions(): Set
    {
        return Set::strings()
            ->unicode()
            ->phoneticExtensions();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->phoneticExtensionsSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phoneticExtensionsSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->phoneticExtensionsSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->combiningDiacriticalMarksSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarksSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->combiningDiacriticalMarksSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->latinExtendedAdditional() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedAdditional(): Set
    {
        return Set::strings()
            ->unicode()
            ->latinExtendedAdditional();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->greekExtended() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function greekExtended(): Set
    {
        return Set::strings()
            ->unicode()
            ->greekExtended();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->generalPunctuation() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function generalPunctuation(): Set
    {
        return Set::strings()
            ->unicode()
            ->generalPunctuation();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->superscriptsAndSubscripts() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function superscriptsAndSubscripts(): Set
    {
        return Set::strings()
            ->unicode()
            ->superscriptsAndSubscripts();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->currencySymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function currencySymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->currencySymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->combiningDiacriticalMarksForSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningDiacriticalMarksForSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->combiningDiacriticalMarksForSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->letterlikeSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function letterlikeSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->letterlikeSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->numberForms() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function numberForms(): Set
    {
        return Set::strings()
            ->unicode()
            ->numberForms();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->arrows() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arrows(): Set
    {
        return Set::strings()
            ->unicode()
            ->arrows();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mathematicalOperators() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mathematicalOperators(): Set
    {
        return Set::strings()
            ->unicode()
            ->mathematicalOperators();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->miscellaneousTechnical() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousTechnical(): Set
    {
        return Set::strings()
            ->unicode()
            ->miscellaneousTechnical();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->controlPictures() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function controlPictures(): Set
    {
        return Set::strings()
            ->unicode()
            ->controlPictures();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->opticalCharacterRecognition() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function opticalCharacterRecognition(): Set
    {
        return Set::strings()
            ->unicode()
            ->opticalCharacterRecognition();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->enclosedAlphanumerics() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedAlphanumerics(): Set
    {
        return Set::strings()
            ->unicode()
            ->enclosedAlphanumerics();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->boxDrawing() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function boxDrawing(): Set
    {
        return Set::strings()
            ->unicode()
            ->boxDrawing();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->blockElements() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function blockElements(): Set
    {
        return Set::strings()
            ->unicode()
            ->blockElements();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->geometricShapes() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function geometricShapes(): Set
    {
        return Set::strings()
            ->unicode()
            ->geometricShapes();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->miscellaneousSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->miscellaneousSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->dingbats() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function dingbats(): Set
    {
        return Set::strings()
            ->unicode()
            ->dingbats();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->miscellaneousMathematicalSymbolsA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousMathematicalSymbolsA(): Set
    {
        return Set::strings()
            ->unicode()
            ->miscellaneousMathematicalSymbolsA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->supplementalArrowsA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalArrowsA(): Set
    {
        return Set::strings()
            ->unicode()
            ->supplementalArrowsA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->braillePatterns() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function braillePatterns(): Set
    {
        return Set::strings()
            ->unicode()
            ->braillePatterns();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->supplementalArrowsB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalArrowsB(): Set
    {
        return Set::strings()
            ->unicode()
            ->supplementalArrowsB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->miscellaneousMathematicalSymbolsB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousMathematicalSymbolsB(): Set
    {
        return Set::strings()
            ->unicode()
            ->miscellaneousMathematicalSymbolsB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->supplementalMathematicalOperators() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalMathematicalOperators(): Set
    {
        return Set::strings()
            ->unicode()
            ->supplementalMathematicalOperators();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->miscellaneousSymbolsAndArrows() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousSymbolsAndArrows(): Set
    {
        return Set::strings()
            ->unicode()
            ->miscellaneousSymbolsAndArrows();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->glagolitic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function glagolitic(): Set
    {
        return Set::strings()
            ->unicode()
            ->glagolitic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->latinExtendedC() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedC(): Set
    {
        return Set::strings()
            ->unicode()
            ->latinExtendedC();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->coptic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function coptic(): Set
    {
        return Set::strings()
            ->unicode()
            ->coptic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->georgianSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function georgianSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->georgianSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tifinagh() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tifinagh(): Set
    {
        return Set::strings()
            ->unicode()
            ->tifinagh();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ethiopicExtended() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopicExtended(): Set
    {
        return Set::strings()
            ->unicode()
            ->ethiopicExtended();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cyrillicExtendedA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicExtendedA(): Set
    {
        return Set::strings()
            ->unicode()
            ->cyrillicExtendedA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->supplementalPunctuation() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalPunctuation(): Set
    {
        return Set::strings()
            ->unicode()
            ->supplementalPunctuation();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkRadicalsSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkRadicalsSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkRadicalsSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kangxiRadicals() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kangxiRadicals(): Set
    {
        return Set::strings()
            ->unicode()
            ->kangxiRadicals();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ideographicDescriptionCharacters() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ideographicDescriptionCharacters(): Set
    {
        return Set::strings()
            ->unicode()
            ->ideographicDescriptionCharacters();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkSymbolsAndPunctuation() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkSymbolsAndPunctuation(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkSymbolsAndPunctuation();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hiragana() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hiragana(): Set
    {
        return Set::strings()
            ->unicode()
            ->hiragana();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->katakana() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function katakana(): Set
    {
        return Set::strings()
            ->unicode()
            ->katakana();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->bopomofo() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bopomofo(): Set
    {
        return Set::strings()
            ->unicode()
            ->bopomofo();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hangulCompatibilityJamo() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulCompatibilityJamo(): Set
    {
        return Set::strings()
            ->unicode()
            ->hangulCompatibilityJamo();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kanbun() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kanbun(): Set
    {
        return Set::strings()
            ->unicode()
            ->kanbun();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->bopomofoExtended() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bopomofoExtended(): Set
    {
        return Set::strings()
            ->unicode()
            ->bopomofoExtended();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkStrokes() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkStrokes(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkStrokes();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->katakanaPhoneticExtensions() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function katakanaPhoneticExtensions(): Set
    {
        return Set::strings()
            ->unicode()
            ->katakanaPhoneticExtensions();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->enclosedCJKLettersAndMonths() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedCJKLettersAndMonths(): Set
    {
        return Set::strings()
            ->unicode()
            ->enclosedCJKLettersAndMonths();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkCompatibility() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibility(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkCompatibility();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkUnifiedIdeographsExtensionA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionA(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkUnifiedIdeographsExtensionA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->yijingHexagramSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function yijingHexagramSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->yijingHexagramSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkUnifiedIdeographs() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographs(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkUnifiedIdeographs();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->yiSyllables() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function yiSyllables(): Set
    {
        return Set::strings()
            ->unicode()
            ->yiSyllables();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->yiRadicals() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function yiRadicals(): Set
    {
        return Set::strings()
            ->unicode()
            ->yiRadicals();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->lisu() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lisu(): Set
    {
        return Set::strings()
            ->unicode()
            ->lisu();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->vai() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function vai(): Set
    {
        return Set::strings()
            ->unicode()
            ->vai();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cyrillicExtendedB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cyrillicExtendedB(): Set
    {
        return Set::strings()
            ->unicode()
            ->cyrillicExtendedB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->bamum() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bamum(): Set
    {
        return Set::strings()
            ->unicode()
            ->bamum();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->modifierToneLetters() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function modifierToneLetters(): Set
    {
        return Set::strings()
            ->unicode()
            ->modifierToneLetters();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->latinExtendedD() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedD(): Set
    {
        return Set::strings()
            ->unicode()
            ->latinExtendedD();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->sylotiNagri() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sylotiNagri(): Set
    {
        return Set::strings()
            ->unicode()
            ->sylotiNagri();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->commonIndicNumberForms() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function commonIndicNumberForms(): Set
    {
        return Set::strings()
            ->unicode()
            ->commonIndicNumberForms();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->phagsPa() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phagsPa(): Set
    {
        return Set::strings()
            ->unicode()
            ->phagsPa();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->saurashtra() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function saurashtra(): Set
    {
        return Set::strings()
            ->unicode()
            ->saurashtra();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->devanagariExtended() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function devanagariExtended(): Set
    {
        return Set::strings()
            ->unicode()
            ->devanagariExtended();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kayahLi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kayahLi(): Set
    {
        return Set::strings()
            ->unicode()
            ->kayahLi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->rejang() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function rejang(): Set
    {
        return Set::strings()
            ->unicode()
            ->rejang();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hangulJamoExtendedA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulJamoExtendedA(): Set
    {
        return Set::strings()
            ->unicode()
            ->hangulJamoExtendedA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->javanese() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function javanese(): Set
    {
        return Set::strings()
            ->unicode()
            ->javanese();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->myanmarExtendedB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function myanmarExtendedB(): Set
    {
        return Set::strings()
            ->unicode()
            ->myanmarExtendedB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cham() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cham(): Set
    {
        return Set::strings()
            ->unicode()
            ->cham();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->myanmarExtendedA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function myanmarExtendedA(): Set
    {
        return Set::strings()
            ->unicode()
            ->myanmarExtendedA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->taiViet() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiViet(): Set
    {
        return Set::strings()
            ->unicode()
            ->taiViet();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->meeteiMayekExtensions() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meeteiMayekExtensions(): Set
    {
        return Set::strings()
            ->unicode()
            ->meeteiMayekExtensions();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ethiopicExtendedA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ethiopicExtendedA(): Set
    {
        return Set::strings()
            ->unicode()
            ->ethiopicExtendedA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->latinExtendedE() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function latinExtendedE(): Set
    {
        return Set::strings()
            ->unicode()
            ->latinExtendedE();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cherokeeSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cherokeeSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->cherokeeSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->meeteiMayek() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meeteiMayek(): Set
    {
        return Set::strings()
            ->unicode()
            ->meeteiMayek();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hangulSyllables() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulSyllables(): Set
    {
        return Set::strings()
            ->unicode()
            ->hangulSyllables();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hangulJamoExtendedB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hangulJamoExtendedB(): Set
    {
        return Set::strings()
            ->unicode()
            ->hangulJamoExtendedB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkCompatibilityIdeographs() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibilityIdeographs(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkCompatibilityIdeographs();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->alphabeticPresentationForms() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function alphabeticPresentationForms(): Set
    {
        return Set::strings()
            ->unicode()
            ->alphabeticPresentationForms();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->arabicPresentationFormsA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicPresentationFormsA(): Set
    {
        return Set::strings()
            ->unicode()
            ->arabicPresentationFormsA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->variationSelectors() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function variationSelectors(): Set
    {
        return Set::strings()
            ->unicode()
            ->variationSelectors();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->verticalForms() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function verticalForms(): Set
    {
        return Set::strings()
            ->unicode()
            ->verticalForms();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->combiningHalfMarks() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function combiningHalfMarks(): Set
    {
        return Set::strings()
            ->unicode()
            ->combiningHalfMarks();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkCompatibilityForms() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibilityForms(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkCompatibilityForms();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->smallFormVariants() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function smallFormVariants(): Set
    {
        return Set::strings()
            ->unicode()
            ->smallFormVariants();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->arabicPresentationFormsB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicPresentationFormsB(): Set
    {
        return Set::strings()
            ->unicode()
            ->arabicPresentationFormsB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->halfwidthAndFullwidthForms() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function halfwidthAndFullwidthForms(): Set
    {
        return Set::strings()
            ->unicode()
            ->halfwidthAndFullwidthForms();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->specials() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function specials(): Set
    {
        return Set::strings()
            ->unicode()
            ->specials();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->linearBSyllabary() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function linearBSyllabary(): Set
    {
        return Set::strings()
            ->unicode()
            ->linearBSyllabary();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->linearBIdeograms() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function linearBIdeograms(): Set
    {
        return Set::strings()
            ->unicode()
            ->linearBIdeograms();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->aeganNumbers() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function aeganNumbers(): Set
    {
        return Set::strings()
            ->unicode()
            ->aeganNumbers();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ancientGreekNumbers() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ancientGreekNumbers(): Set
    {
        return Set::strings()
            ->unicode()
            ->ancientGreekNumbers();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ancientSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ancientSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->ancientSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->phaistosDisc() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phaistosDisc(): Set
    {
        return Set::strings()
            ->unicode()
            ->phaistosDisc();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->lycian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lycian(): Set
    {
        return Set::strings()
            ->unicode()
            ->lycian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->carian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function carian(): Set
    {
        return Set::strings()
            ->unicode()
            ->carian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->copticEpactNumbers() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function copticEpactNumbers(): Set
    {
        return Set::strings()
            ->unicode()
            ->copticEpactNumbers();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oldItalic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldItalic(): Set
    {
        return Set::strings()
            ->unicode()
            ->oldItalic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->gothic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function gothic(): Set
    {
        return Set::strings()
            ->unicode()
            ->gothic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oldPermic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldPermic(): Set
    {
        return Set::strings()
            ->unicode()
            ->oldPermic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ugaritic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ugaritic(): Set
    {
        return Set::strings()
            ->unicode()
            ->ugaritic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oldPersian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldPersian(): Set
    {
        return Set::strings()
            ->unicode()
            ->oldPersian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->deseret() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function deseret(): Set
    {
        return Set::strings()
            ->unicode()
            ->deseret();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->shavian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function shavian(): Set
    {
        return Set::strings()
            ->unicode()
            ->shavian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->osmanya() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function osmanya(): Set
    {
        return Set::strings()
            ->unicode()
            ->osmanya();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->osage() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function osage(): Set
    {
        return Set::strings()
            ->unicode()
            ->osage();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->elbasan() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function elbasan(): Set
    {
        return Set::strings()
            ->unicode()
            ->elbasan();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->caucasianAlbanian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function caucasianAlbanian(): Set
    {
        return Set::strings()
            ->unicode()
            ->caucasianAlbanian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->linearA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function linearA(): Set
    {
        return Set::strings()
            ->unicode()
            ->linearA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cypriotSyllabary() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cypriotSyllabary(): Set
    {
        return Set::strings()
            ->unicode()
            ->cypriotSyllabary();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->imperialAramaic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function imperialAramaic(): Set
    {
        return Set::strings()
            ->unicode()
            ->imperialAramaic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->palmyrene() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function palmyrene(): Set
    {
        return Set::strings()
            ->unicode()
            ->palmyrene();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->nabataean() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function nabataean(): Set
    {
        return Set::strings()
            ->unicode()
            ->nabataean();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->hatran() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function hatran(): Set
    {
        return Set::strings()
            ->unicode()
            ->hatran();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->phoenician() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function phoenician(): Set
    {
        return Set::strings()
            ->unicode()
            ->phoenician();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->lydian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function lydian(): Set
    {
        return Set::strings()
            ->unicode()
            ->lydian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->meroiticHieroglyphs() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meroiticHieroglyphs(): Set
    {
        return Set::strings()
            ->unicode()
            ->meroiticHieroglyphs();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->meroiticCursive() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function meroiticCursive(): Set
    {
        return Set::strings()
            ->unicode()
            ->meroiticCursive();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kharoshthi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kharoshthi(): Set
    {
        return Set::strings()
            ->unicode()
            ->kharoshthi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oldSouthArabian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldSouthArabian(): Set
    {
        return Set::strings()
            ->unicode()
            ->oldSouthArabian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oldNorthArabian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldNorthArabian(): Set
    {
        return Set::strings()
            ->unicode()
            ->oldNorthArabian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->manichaean() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function manichaean(): Set
    {
        return Set::strings()
            ->unicode()
            ->manichaean();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->avestan() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function avestan(): Set
    {
        return Set::strings()
            ->unicode()
            ->avestan();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->inscriptionalParthian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function inscriptionalParthian(): Set
    {
        return Set::strings()
            ->unicode()
            ->inscriptionalParthian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->inscriptionalPahlavi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function inscriptionalPahlavi(): Set
    {
        return Set::strings()
            ->unicode()
            ->inscriptionalPahlavi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->psalterPahlavi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function psalterPahlavi(): Set
    {
        return Set::strings()
            ->unicode()
            ->psalterPahlavi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oldTurkic() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldTurkic(): Set
    {
        return Set::strings()
            ->unicode()
            ->oldTurkic();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->oldHungarian() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function oldHungarian(): Set
    {
        return Set::strings()
            ->unicode()
            ->oldHungarian();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->rumiNumeralSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function rumiNumeralSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->rumiNumeralSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->brahmi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function brahmi(): Set
    {
        return Set::strings()
            ->unicode()
            ->brahmi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kaithi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kaithi(): Set
    {
        return Set::strings()
            ->unicode()
            ->kaithi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->soraSompeg() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function soraSompeg(): Set
    {
        return Set::strings()
            ->unicode()
            ->soraSompeg();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->chakma() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function chakma(): Set
    {
        return Set::strings()
            ->unicode()
            ->chakma();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mahajani() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mahajani(): Set
    {
        return Set::strings()
            ->unicode()
            ->mahajani();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->sharada() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sharada(): Set
    {
        return Set::strings()
            ->unicode()
            ->sharada();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->sinhalaArchaicNumbers() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function sinhalaArchaicNumbers(): Set
    {
        return Set::strings()
            ->unicode()
            ->sinhalaArchaicNumbers();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->khojki() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khojki(): Set
    {
        return Set::strings()
            ->unicode()
            ->khojki();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->multani() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function multani(): Set
    {
        return Set::strings()
            ->unicode()
            ->multani();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->khudawadi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function khudawadi(): Set
    {
        return Set::strings()
            ->unicode()
            ->khudawadi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->grantha() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function grantha(): Set
    {
        return Set::strings()
            ->unicode()
            ->grantha();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->newa() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function newa(): Set
    {
        return Set::strings()
            ->unicode()
            ->newa();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tirhuta() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tirhuta(): Set
    {
        return Set::strings()
            ->unicode()
            ->tirhuta();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->siddham() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function siddham(): Set
    {
        return Set::strings()
            ->unicode()
            ->siddham();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->modi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function modi(): Set
    {
        return Set::strings()
            ->unicode()
            ->modi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mongolianSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mongolianSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->mongolianSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->takri() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function takri(): Set
    {
        return Set::strings()
            ->unicode()
            ->takri();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ahom() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ahom(): Set
    {
        return Set::strings()
            ->unicode()
            ->ahom();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->warangCiti() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function warangCiti(): Set
    {
        return Set::strings()
            ->unicode()
            ->warangCiti();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->zanabazarSquare() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function zanabazarSquare(): Set
    {
        return Set::strings()
            ->unicode()
            ->zanabazarSquare();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->soyombo() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function soyombo(): Set
    {
        return Set::strings()
            ->unicode()
            ->soyombo();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->pauCinHau() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function pauCinHau(): Set
    {
        return Set::strings()
            ->unicode()
            ->pauCinHau();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->bhaiksuki() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bhaiksuki(): Set
    {
        return Set::strings()
            ->unicode()
            ->bhaiksuki();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->marchen() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function marchen(): Set
    {
        return Set::strings()
            ->unicode()
            ->marchen();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->masaramGondi() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function masaramGondi(): Set
    {
        return Set::strings()
            ->unicode()
            ->masaramGondi();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cuneiform() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cuneiform(): Set
    {
        return Set::strings()
            ->unicode()
            ->cuneiform();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cuneiformNumbersAndPunctuation() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cuneiformNumbersAndPunctuation(): Set
    {
        return Set::strings()
            ->unicode()
            ->cuneiformNumbersAndPunctuation();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->earlyDynasticCuneiform() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function earlyDynasticCuneiform(): Set
    {
        return Set::strings()
            ->unicode()
            ->earlyDynasticCuneiform();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->egyptianHieroglyphs() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function egyptianHieroglyphs(): Set
    {
        return Set::strings()
            ->unicode()
            ->egyptianHieroglyphs();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->anatolianHieroglyphs() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function anatolianHieroglyphs(): Set
    {
        return Set::strings()
            ->unicode()
            ->anatolianHieroglyphs();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->bamumSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bamumSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->bamumSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mro() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mro(): Set
    {
        return Set::strings()
            ->unicode()
            ->mro();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->bassaVah() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function bassaVah(): Set
    {
        return Set::strings()
            ->unicode()
            ->bassaVah();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->pahawhHmong() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function pahawhHmong(): Set
    {
        return Set::strings()
            ->unicode()
            ->pahawhHmong();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->miao() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miao(): Set
    {
        return Set::strings()
            ->unicode()
            ->miao();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ideographicSymbolsAndPunctuation() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ideographicSymbolsAndPunctuation(): Set
    {
        return Set::strings()
            ->unicode()
            ->ideographicSymbolsAndPunctuation();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tangut() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tangut(): Set
    {
        return Set::strings()
            ->unicode()
            ->tangut();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tangutComponents() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tangutComponents(): Set
    {
        return Set::strings()
            ->unicode()
            ->tangutComponents();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kanaSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kanaSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->kanaSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->kanaExtendedA() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function kanaExtendedA(): Set
    {
        return Set::strings()
            ->unicode()
            ->kanaExtendedA();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->nushu() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function nushu(): Set
    {
        return Set::strings()
            ->unicode()
            ->nushu();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->duployan() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function duployan(): Set
    {
        return Set::strings()
            ->unicode()
            ->duployan();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->shorthandFormatControls() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function shorthandFormatControls(): Set
    {
        return Set::strings()
            ->unicode()
            ->shorthandFormatControls();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->byzantineMusicalSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function byzantineMusicalSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->byzantineMusicalSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->musicalSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function musicalSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->musicalSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ancientGreekMusicalNotation() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ancientGreekMusicalNotation(): Set
    {
        return Set::strings()
            ->unicode()
            ->ancientGreekMusicalNotation();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->taiXuanJingSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function taiXuanJingSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->taiXuanJingSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->countingRodNumerals() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function countingRodNumerals(): Set
    {
        return Set::strings()
            ->unicode()
            ->countingRodNumerals();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mathematicalAlphanumericSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mathematicalAlphanumericSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->mathematicalAlphanumericSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->suttonSignWriting() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function suttonSignWriting(): Set
    {
        return Set::strings()
            ->unicode()
            ->suttonSignWriting();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->glagoliticSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function glagoliticSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->glagoliticSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mendeKikakui() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mendeKikakui(): Set
    {
        return Set::strings()
            ->unicode()
            ->mendeKikakui();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->adlam() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function adlam(): Set
    {
        return Set::strings()
            ->unicode()
            ->adlam();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->arabicMathematicalAlphabeticSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function arabicMathematicalAlphabeticSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->arabicMathematicalAlphabeticSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->mahjongTiles() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function mahjongTiles(): Set
    {
        return Set::strings()
            ->unicode()
            ->mahjongTiles();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->dominoTiles() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function dominoTiles(): Set
    {
        return Set::strings()
            ->unicode()
            ->dominoTiles();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->playingCards() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function playingCards(): Set
    {
        return Set::strings()
            ->unicode()
            ->playingCards();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->enclosedAlphanumericSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedAlphanumericSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->enclosedAlphanumericSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->enclosedIdeopgraphicSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function enclosedIdeopgraphicSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->enclosedIdeopgraphicSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->miscellaneousSymbolsAndPictographs() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function miscellaneousSymbolsAndPictographs(): Set
    {
        return Set::strings()
            ->unicode()
            ->miscellaneousSymbolsAndPictographs();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->emoticons() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function emoticons(): Set
    {
        return Set::strings()
            ->unicode()
            ->emoticons();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->ornamentalDingbats() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function ornamentalDingbats(): Set
    {
        return Set::strings()
            ->unicode()
            ->ornamentalDingbats();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->transportAndMapSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function transportAndMapSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->transportAndMapSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->alchemicalSymbols() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function alchemicalSymbols(): Set
    {
        return Set::strings()
            ->unicode()
            ->alchemicalSymbols();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->geometricShapesExtended() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function geometricShapesExtended(): Set
    {
        return Set::strings()
            ->unicode()
            ->geometricShapesExtended();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->supplementalArrowsC() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalArrowsC(): Set
    {
        return Set::strings()
            ->unicode()
            ->supplementalArrowsC();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->supplementalSymbolsAndPictographs() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function supplementalSymbolsAndPictographs(): Set
    {
        return Set::strings()
            ->unicode()
            ->supplementalSymbolsAndPictographs();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkUnifiedIdeographsExtensionB() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionB(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkUnifiedIdeographsExtensionB();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkUnifiedIdeographsExtensionC() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionC(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkUnifiedIdeographsExtensionC();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkUnifiedIdeographsExtensionD() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionD(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkUnifiedIdeographsExtensionD();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkUnifiedIdeographsExtensionE() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionE(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkUnifiedIdeographsExtensionE();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkUnifiedIdeographsExtensionF() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkUnifiedIdeographsExtensionF(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkUnifiedIdeographsExtensionF();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->cjkCompatibilityIdeographsSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function cjkCompatibilityIdeographsSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->cjkCompatibilityIdeographsSupplement();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->tags() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function tags(): Set
    {
        return Set::strings()
            ->unicode()
            ->tags();
    }

    /**
     * @deprecated Use Set::strings()->unicode()->variationSelectorsSupplement() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function variationSelectorsSupplement(): Set
    {
        return Set::strings()
            ->unicode()
            ->variationSelectorsSupplement();
    }
}
