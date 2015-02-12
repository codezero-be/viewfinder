<?php namespace CodeZero\ViewFinder;

interface Localizer {

    /**
     * Set PHP's Locale Category
     *
     * @param int $category
     */
    public function setPhpLocaleCategory($category);

    /**
     * Set the active locale
     *
     * @param string $locale
     */
    public function setLocale($locale = null);

    /**
     * Set the list of locales
     *
     * @param array $locales
     */
    public function setLocales(array $locales);

    /**
     * Get the active locale
     *
     * @return string
     */
    public function getLocale();

    /**
     * Get PHP's active locale
     *
     * @return string
     */
    public function getPhpLocale();

    /**
     * Get the list of locales
     *
     * @return array
     */
    public function getLocales();

    /**
     * Get the requested locale
     *
     * @return string
     */
    public function getRequestedLocale();

    /**
     * Check if a locale is valid
     *
     * @param string $locale
     *
     * @return bool
     */
    public function isLocaleValid($locale);

    /**
     * Check if the requested locale is valid
     *
     * @return bool
     */
    public function isRequestedLocaleValid();

}