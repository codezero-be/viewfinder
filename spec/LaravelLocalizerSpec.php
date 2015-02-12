<?php namespace spec\CodeZero\ViewFinder;

use Illuminate\Http\Request;
use Illuminate\Translation\Translator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelLocalizerSpec extends ObjectBehavior {

    private static $LOCALES = ['nl' => 'nl_BE.utf8', 'en' => 'en_US.utf8'];
    private static $LOCALES_ENGLISH = ['en' => 'en_US.utf8', 'nl' => 'nl_BE.utf8'];
    private static $PHPCATEGORY = 1;

    function let(Translator $translator, Request $request)
    {
        $this->beConstructedWith(self::$LOCALES, self::$PHPCATEGORY, $translator, $request);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\ViewFinder\LaravelLocalizer');
    }

    function it_gets_the_requested_locale(Request $request)
    {
        $request->segment(1)->shouldBeCalled()->willReturn('en');
        $this->getRequestedLocale()->shouldReturn('en');
    }

    function it_gets_the_active_locale(Translator $translator)
    {
        $translator->getLocale()->shouldBeCalled()->willReturn('en');
        $this->getLocale()->shouldReturn('en');
    }

    function it_gets_an_array_of_locales()
    {
        $this->getLocales()->shouldReturn(self::$LOCALES);
    }

    function it_sets_an_array_of_locales()
    {
        $locales = ['nl' => 'nl_BE.utf8', 'en' => 'en_US.utf8', 'fr' => 'fr_FR.utf8'];

        $this->setLocales($locales);
        $this->getLocales()->shouldReturn($locales);
    }

    function it_enforces_lower_case_locale_array_keys()
    {
        $locales1 = ['NL' => 'nl_BE.utf8', 'en' => 'en_US.utf8', 'Fr' => 'fr_FR.utf8'];
        $locales2 = ['nl' => 'nl_BE.utf8', 'en' => 'en_US.utf8', 'fr' => 'fr_FR.utf8'];

        $this->setLocales($locales1);
        $this->getLocales()->shouldReturn($locales2);
    }

    function it_checks_if_a_locale_is_valid()
    {
        $this->isLocaleValid('en')->shouldBe(true);
        $this->isLocaleValid('dk')->shouldBe(false);
    }

    function it_checks_if_a_requested_locale_is_valid(Request $request)
    {
        $request->segment(1)->shouldBeCalled()->willReturn('en');
        $this->isRequestedLocaleValid()->shouldBe(true);

        $request->segment(1)->shouldBeCalled()->willReturn('dk');
        $this->isRequestedLocaleValid()->shouldBe(false);
    }

    function it_sets_a_locale()
    {
        $this->setLocale('en');
        $this->getLocales()->shouldReturn(self::$LOCALES_ENGLISH);
    }

    function it_sets_a_locale_in_lower_case()
    {
        $this->setLocale('EN');
        $this->getLocales()->shouldReturn(self::$LOCALES_ENGLISH);
    }

    function it_sets_a_requested_locale(Request $request)
    {
        $request->segment(1)->shouldBeCalled()->willReturn('en');
        $this->setLocale();
        $this->getLocales()->shouldReturn(self::$LOCALES_ENGLISH);
    }

    function it_sets_laravels_locale_and_fallback_locale(Translator $translator)
    {
        $locale = 'en';
        $fallback = 'nl';

        $translator->setLocale($locale)->shouldBeCalled();
        $translator->setFallback($fallback)->shouldBeCalled();
        $this->setLocale($locale);
    }

    function it_sets_the_php_locale()
    {
        $locale = 'en';

        $this->setLocale($locale);
        $this->getPhpLocale()->shouldReturn('en_US.utf8');
    }

}