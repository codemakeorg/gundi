<?php
namespace Core\Library\Language;

use Symfony\Component\Translation\TranslatorInterface;


/**
 * todo:: this class stub of TranslatorInterface. Write realization
 * Class Translator
 * @package Core\Library\Language
 */
class Translator implements TranslatorInterface
{
    protected $sLocale;
    /**
     * Translates the given message.
     *
     * @param string $sId The message id (may also be an object that can be cast to string)
     * @param array $aParameters An array of parameters for the message
     * @param string|null $sDomain The domain for the message or null to use the default
     * @param string|null $sLocale The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function trans($sId, array $aParameters = [], $sDomain = null, $sLocale = null)
    {
        return $sId; //todo::translate phrase
    }

    /**
     * Translates the given choice message by choosing a translation according to a number.
     *
     * @param string $sId The message id (may also be an object that can be cast to string)
     * @param int $iNumber The number to use to find the indice of the message
     * @param array $aParameters An array of parameters for the message
     * @param string|null $sDomain The domain for the message or null to use the default
     * @param string|null $sLocale The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function transChoice($sId, $iNumber, array $aParameters = [], $sDomain = null, $sLocale = null)
    {
        return $sId; //todo::translate phrase
    }

    /**
     * Sets the current locale.
     *
     * @param string $sLocale The locale
     *
     * @throws \InvalidArgumentException If the locale contains invalid characters
     */
    public function setLocale($sLocale)
    {
        $this->sLocale = $sLocale;
    }

    /**
     * Returns the current locale.
     *
     * @return string The locale
     */
    public function getLocale()
    {
        return $this->sLocale;
    }
}