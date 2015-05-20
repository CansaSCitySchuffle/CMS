<?php

namespace spec\lucas;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use lucas\Module;
use lucas\ViewFrame;
use lucas\Logger;
use lucas\Request;


class ApplicationSpec extends ObjectBehavior
{
    private $view = "mockViewModel";
    private $action = "action";

    function it_is_initializable()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->shouldHaveType('lucas\Application');
    }

    function it_calls_the_corrrect_viewframe_by_key(ViewFrame $viewFrame) {
        $this->addViewFrame($this->view, $viewFrame);
        $viewFrame->serve(Argument::type('lucas\Request'))->shouldBeCalled();

        $_GET['view'] = $this->view;

        $this->serve();
    }

    function it_passes_a_generated_request_object_to_the_corresponding_viewframe(ViewFrame $viewFrame) {
        $this->addViewFrame($this->view, $viewFrame);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['view'] = $this->view;
        $expectedRequest = new Request();
        $expectedRequest->method = 'GET';
        $expectedRequest->view = $this->view;

        $viewFrame->serve($expectedRequest)->shouldBeCalled();

        $this->serve();
    }

    function it_is_possible_to_pass_a_correlation_id() {
        $expectedCorrelationId = "lkajdsfkjiw123";
        $this->beConstructedWith(null, $expectedCorrelationId);

        $this->getCorrelationId()->shouldReturn($expectedCorrelationId);
    }

    function it_generates_for_every_new_instatiation_a_new_id() {
        $app1 = new \lucas\Application();
        $otherCorrelationId = $app1->getCorrelationId();

        $this->getCorrelationId()->shouldNotReturn($otherCorrelationId);
    }

    function it_should_throw_an_Exception_if_there_is_no_viewFrame_for_requested_Key() {
        $this->shouldThrow('\Exception')->during('serve', array($this->view, $this->action));
    }

    function it_loggs_Exception_with_correlation_and_request_object(
        Logger $logger, ViewFrame $viewFrame
    ) {
        $expectedCorrelationId = "korrelationID";

        $this->beConstructedWith($logger, $expectedCorrelationId);
        $this->addViewFrame($this->view, $viewFrame);
        $viewFrame->serve(Argument::any())->willThrow('\Exception');


        $logger->fatal($expectedCorrelationId, null)->shouldBeCalled();

        $this->serve($this->view, $this->action);
    }
}
