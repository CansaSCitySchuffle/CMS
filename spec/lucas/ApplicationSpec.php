<?php

namespace spec\lucas;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use lucas\Module;
use lucas\ViewFrame;
use lucas\Logger;


class ApplicationSpec extends ObjectBehavior
{
    private $view = "mockViewModel";
    private $action = "action";

    function it_is_initializable()
    {
        $this->shouldHaveType('lucas\Application');
    }

    function it_calls_the_corrrect_viewframe_by_key(ViewFrame $viewFrame) {
        $this->addViewFrame($this->view, $viewFrame);
        $viewFrame->serve($this->action)->shouldBeCalled();

        $this->serve($this->view, $this->action);
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
        $expectedUserId = "guest";

        $this->beConstructedWith($logger, $expectedCorrelationId);
        $this->addViewFrame($this->view, $viewFrame);
        $viewFrame->serve(Argument::any())->willThrow('\Exception');


        $logger->fatal($expectedCorrelationId, $expectedUserId, null)->shouldBeCalled();

        $this->serve($this->view, $this->action);
    }
}
