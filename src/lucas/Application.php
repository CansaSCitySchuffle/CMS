<?php

namespace lucas;

class Application
{
    private $viewModels = array();

    private $correlation_id = null;
    private $logger = null;

    public function __construct(Logger $logger = null, $correlation_id = null) {
        if ($correlation_id != null) {
            $this->correlation_id = $correlation_id;
        } else {
            $this->correlation_id = self::generateCorrelationId();
        }

        $this->logger = $logger;
    }

    private static function generateCorrelationId() {
        return \uniqid();
    }

    public function getCorrelationId() {
        return $this->correlation_id;
    }

    public function addModule($key, $module)
    {
        throw new \Exception("Not Implemented");
    }

    public function addViewFrame($key, $viewModel)
    {
        $this->viewModels[$key] = $viewModel;
    }

    public function serve() {
        $view = $_GET['view'];
        $page = $_GET['page'];

        if (!array_key_exists($view, $this->viewModels)) {
            throw new \Exception("No view parameter delivered!");
        }

        $viewModel = $this->viewModels[$view];

        $request = new Request();
        $request->view = $view;
        $request->method = $_SERVER['REQUEST_METHOD'];
        $request->page = $page;

        try {
            $viewModel->serve($request);
        } catch (\Exception $e) {
            if ($e instanceof \Prophecy\Exception\Call\UnexpectedCallException) {
                throw $e;
            }

            $this->logger->fatal($this->correlation_id,  $request);
        }
    }
}
