<?php namespace spec\CodeZero\ViewFinder;

use CodeZero\ViewFinder\ViewFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ViewFinderSpec extends ObjectBehavior {

    private $viewFactory;

    function let(ViewFactory $viewFactory)
    {
        $this->beAnInstanceOf('CodeZero\ViewFinder\ViewFinder');
        $this->beConstructedWith($viewFactory, ['nl', 'en'], '.');

        $this->viewFactory = $viewFactory;
    }

    function it_returns_a_view_from_the_factory()
    {
        // Simulate checking if a view exists
        $this->shouldCheckIfViewExists('foo', true);

        // Simulate making of a view
        $this->viewFactory->make('foo', [], [])
            ->shouldBeCalled()
            ->willReturn('the requested view');

        $this->make('foo')->shouldReturn('the requested view');
    }

    function it_returns_a_matching_view_name()
    {
        // Simulate checking if a view exists
        $this->shouldCheckIfViewExists('foo', false);
        $this->shouldCheckIfViewExists('nl.foo', false);
        $this->shouldCheckIfViewExists('en.foo', true);

        $this->findMatchingViewName('foo')->shouldReturn('en.foo');
    }

    function it_throws_if_no_matching_view_is_found()
    {
        // Simulate checking if a view exists
        $this->shouldCheckIfViewExists('foo', false);
        $this->shouldCheckIfViewExists('nl.foo', false);
        $this->shouldCheckIfViewExists('en.foo', false);

        $this->shouldThrow('CodeZero\ViewFinder\ViewNotFoundException')
             ->duringFindMatchingViewName('foo');
    }

    function it_lists_possible_view_names()
    {
        $expectedValue = ['foo', 'nl.foo', 'en.foo'];

        $this->listPossibleViewNames('foo')->shouldReturn($expectedValue);
    }

    function it_lists_possible_view_names_custom_dividers()
    {
        $divider = '/';
        $expectedValue = ['foo', 'nl/foo', 'en/foo'];

        $this->listPossibleViewNames('foo', null, $divider)->shouldReturn($expectedValue);
    }

    function it_lists_possible_view_names_with_custom_prefixes()
    {
        $prefixes = ['a', 'b', 'c'];
        $expectedValue = ['foo', 'a.foo', 'b.foo', 'c.foo'];

        $this->listPossibleViewNames('foo', $prefixes)->shouldReturn($expectedValue);
    }

    private function shouldCheckIfViewExists($view, $exists)
    {
        $this->viewFactory->exists($view)->shouldBeCalled()->willReturn($exists);
    }

}