<?php namespace spec\CodeZero\ViewFinder;

use Illuminate\Config\Repository as Config;
use Illuminate\View\Factory as ViewFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelViewFinderSpec extends ObjectBehavior {

    function let(ViewFactory $viewFactory, Config $config)
    {
        $this->beConstructedWith($viewFactory, $config);
        $this->shouldHaveType('CodeZero\ViewFinder\LaravelViewFinder');
    }

    function it_makes_a_view(ViewFactory $viewFactory)
    {
        // Simulate checking if a view exists
        $viewFactory->exists('foo')->shouldBeCalled()->willReturn(true);

        // Simulate making of a view
        $viewFactory->make('foo', [], [])->shouldBeCalled()->willReturn('bar');

        $this->make('foo')->shouldReturn('bar');
    }

    function it_fetches_a_localized_view_name(ViewFactory $viewFactory, Config $config)
    {
        // Simulate fetching locale from the config file
        $config->get('app.locale')->shouldBeCalled()->willReturn('nl');
        $config->get('app.fallback_locale')->shouldBeCalled()->willReturn('en');

        // Simulate checking if a view exists
        $viewFactory->exists('foo')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('nl.foo')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('en.foo')->shouldBeCalled()->willReturn(true);

        $this->getLocalizedViewName('foo')->shouldReturn('en.foo');
    }

    function it_throws_if_no_localized_view_is_found(ViewFactory $viewFactory, Config $config)
    {
        // Simulate fetching locale from the config file
        $config->get('app.locale')->shouldBeCalled()->willReturn('nl');
        $config->get('app.fallback_locale')->shouldBeCalled()->willReturn('en');

        // Simulate checking if a view exists
        $viewFactory->exists('foo')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('nl.foo')->shouldBeCalled()->willReturn(false);
        $viewFactory->exists('en.foo')->shouldBeCalled()->willReturn(false);

        $this->shouldThrow('CodeZero\ViewFinder\ViewNotFoundException')
             ->duringGetLocalizedViewName('foo');
    }

}