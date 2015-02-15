<?php namespace spec\CodeZero\ViewFinder;

use CodeZero\ViewFinder\Localizer;
use CodeZero\ViewFinder\ViewFactory;
use Illuminate\Contracts\Routing\Registrar as Router;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelViewFinderSpec extends ObjectBehavior {

    private static $LOCALES = ['nl' => 'nl_BE.utf8', 'en' => 'en_US.utf8', 'fr' => 'fr_FR.utf8'];

    function let(ViewFactory $viewFactory, Localizer $localizer, Router $router)
    {
        $this->beConstructedWith($viewFactory, $localizer, $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\ViewFinder\LaravelViewFinder');
    }

    function it_searches_for_a_view(Localizer $localizer, ViewFactory $viewFactory)
    {
        $localizer->getLocales()->shouldBeCalled()->willReturn(self::$LOCALES);
        $localizer->isRequestedLocaleValid()->shouldBeCalled()->willReturn(true);

        $viewFactory->exists('view')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('nl.view')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('en.view')->shouldBeCalled()->willReturn(true);
        $viewFactory->exists('fr.view')->shouldNotBeCalled();

        $viewFactory->make('en.view', [], [])->shouldBeCalled()->willReturn('view');
        $this->make('view')->shouldReturn('view');
    }

    function it_returns_a_regular_view_if_no_locale_is_specified(Localizer $localizer, ViewFactory $viewFactory)
    {
        $localizer->isRequestedLocaleValid()->shouldBeCalled()->willReturn(false);

        $viewFactory->exists('view')->shouldBeCalled()->willReturn(true);
        $viewFactory->exists('nl.view')->shouldNotBeCalled();
        $viewFactory->exists('en.view')->shouldNotBeCalled();
        $viewFactory->exists('fr.view')->shouldNotBeCalled();

        $viewFactory->make('view', [], [])->shouldBeCalled()->willReturn('view');
        $this->make('view')->shouldReturn('view');
    }

    function it_throws_up_if_no_matching_view_could_be_found(Localizer $localizer, ViewFactory $viewFactory)
    {
        $localizer->isRequestedLocaleValid()->shouldBeCalled()->willReturn(false);

        $viewFactory->exists('view')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('nl.view')->shouldNotBeCalled();
        $viewFactory->exists('en.view')->shouldNotBeCalled();
        $viewFactory->exists('fr.view')->shouldNotBeCalled();

        $viewFactory->make('view', [], [])->shouldNotBeCalled();
        $this->shouldThrow('CodeZero\ViewFinder\ViewNotFoundException')->duringMake('view');
    }

    function it_throws_up_if_fallback_views_are_disabled(Localizer $localizer, ViewFactory $viewFactory)
    {
        $localizer->getRequestedLocale()->shouldBeCalled()->willReturn('nl');
        $localizer->isRequestedLocaleValid()->shouldBeCalled()->willReturn(true);

        $viewFactory->exists('view')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('nl.view')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('en.view')->shouldNotBeCalled();
        $viewFactory->exists('fr.view')->shouldNotBeCalled();

        $viewFactory->make('view', [], [])->shouldNotBeCalled();
        $this->shouldThrow('CodeZero\ViewFinder\ViewNotFoundException')->duringMake('view', [], [], true);
    }

    function it_registers_a_route_group_with_the_locale_as_prefix(Localizer $localizer, Router $router)
    {
        $localizer->isRequestedLocaleValid()->shouldBeCalled()->willReturn(true);
        $localizer->getLocale()->shouldBeCalled()->willReturn('en');

        $router->group(['prefix' => 'en'], Argument::type('callable'))->shouldBeCalled();
        $this->routes(function(){});
    }

}