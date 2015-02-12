<?php namespace CodeZero\ViewFinder;

use Illuminate\Http\Request;
use Illuminate\Translation\Translator;

class LaravelLocalizer implements Localizer {

    /**
     * Locales
     *
     * @var array
     */
    private $locales;

    /**
     * PHP's SetLocale Category
     *
     * @var int
     */
    private $phpLocaleCategory;

    /**
     * Laravel's Translator
     *
     * @var Translator
     */
    private $translator;

    /**
     * Laravel's HTTP Request Class
     *
     * @var Request
     */
    private $request;

    /**
     * Create an instance of the Localizer
     *
     * @param array $locales
     * @param int $phpLocaleCategory
     * @param Translator $translator
     * @param Request $request
     */
    public function __construct(array $locales, $phpLocaleCategory, Translator $translator, Request $request)
    {
        $this->translator = $translator;
        $this->request = $request;

        $this->setPhpLocaleCategory($phpLocaleCategory);
        $this->setLocales($locales);
        $this->setLocale();
    }

    /**
     * Set PHP's Locale Category
     *
     * @param int $category
     */
    public function setPhpLocaleCategory($category)
    {
        $this->phpLocaleCategory = $category;
    }

    /**
     * Set the active Laravel and PHP locale
     *
     * @param string $locale
     */
    public function setLocale($locale = null)
    {
        $locale = $locale ?: $this->getRequestedLocale();
        $locale = strtolower($locale);

        if ($this->isLocaleValid($locale))
        {
            $locales = $this->updateLocalesList($locale, $this->locales);

            $this->setLaravelLocale($locale);
            $this->setLaravelFallbackLocale($locales);
            $this->setPhpLocale($locale, $locales);

            $this->locales = $locales;
        }
    }

    /**
     * Set the list of locales
     *
     * @param array $locales
     */
    public function setLocales(array $locales)
    {
        $this->locales = $this->arrayKeysToLower($locales);
    }

    /**
     * Get Laravel's active locale
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * Get PHP's active locale
     *
     * @return string
     */
    public function getPhpLocale()
    {
        return setlocale($this->phpLocaleCategory, 0);
    }

    /**
     * Get the list of locales
     *
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

    /**
     * Get the requested locale
     *
     * @return string
     */
    public function getRequestedLocale()
    {
        return strtolower($this->request->segment(1));
    }

    /**
     * Check if a locale is valid
     *
     * @param string $locale
     *
     * @return bool
     */
    public function isLocaleValid($locale)
    {
        return array_key_exists($locale, $this->getLocales());
    }

    /**
     * Check if the requested locale is valid
     *
     * @return bool
     */
    public function isRequestedLocaleValid()
    {
        return $this->isLocaleValid($this->getRequestedLocale());
    }

    /**
     * Convert all array keys to lower case
     *
     * @param array $array
     *
     * @return array
     */
    private function arrayKeysToLower(array $array)
    {
        $result = [];

        foreach ($array as $key => $value)
        {
            $result[strtolower($key)] = $value;
        }

        return $result;
    }

    /**
     * Update the list of locales with the active one first
     *
     * @param string $locale
     * @param array $locales
     *
     * @return array
     */
    private function updateLocalesList($locale, array $locales)
    {
        // Backup the active locale array element
        $firstLocale = [$locale => $locales[$locale]];
        // Remove the active locale from the array
        unset($locales[$locale]);
        // Put the active locale first in the array
        $updatedLocales = array_merge($firstLocale, $locales);

        return $updatedLocales;
    }

    /**
     * Set Laravel's locale
     *
     * @param string $locale
     */
    private function setLaravelLocale($locale)
    {
        $this->translator->setLocale($locale);
    }

    /**
     * Set Laravel's fallback locale
     *
     * @param array $locales
     */
    private function setLaravelFallbackLocale(array $locales)
    {
        $keys = array_keys($locales);

        if (count($keys) > 1)
        {
            $this->translator->setFallback($keys[1]);
        }
    }

    /**
     * Set PHP's locale for date translations etc.
     *
     * @param string $locale
     * @param array $locales
     */
    private function setPhpLocale($locale, array $locales)
    {
        setlocale($this->phpLocaleCategory, $locales[$locale]);
    }

}