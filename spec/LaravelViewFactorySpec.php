<?php namespace spec\CodeZero\ViewFinder;

use Illuminate\Contracts\View\Factory as IlluminateViewFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelViewFactorySpec extends ObjectBehavior {

    function let(IlluminateViewFactory $viewFactory)
    {
        $this->beConstructedWith($viewFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('CodeZero\ViewFinder\LaravelViewFactory');
    }

    function it_makes_a_view(IlluminateViewFactory $viewFactory)
    {
        $view = 'my.view';

        $viewFactory->make($view, [], [])->shouldBeCalled()->willReturn('view');
        $this->make($view)->shouldReturn('view');
    }

    function it_makes_a_view_with_data(IlluminateViewFactory $viewFactory)
    {
        $view = 'my.view';
        $data = ['my' => 'data'];
        $mergedData = ['merged' => 'data'];

        $viewFactory->make($view, $data, $mergedData)->shouldBeCalled()->willReturn('view');
        $this->make($view, $data, $mergedData)->shouldReturn('view');
    }

    function it_checks_if_a_view_exists(IlluminateViewFactory $viewFactory)
    {
        $view = 'my.view';

        $viewFactory->exists($view)->shouldBeCalled()->willReturn(true);
        $this->exists($view)->shouldReturn(true);
    }

}