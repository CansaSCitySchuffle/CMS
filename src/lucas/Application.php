<?php

namespace lucas;

class Application
{
    private $viewModels = array();
    private $modules = array();

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
        if (array_key_exists($key, $this->modules)) {
            throw new \Exception("Module {$this->modules} allready exists.");
        }

        $this->modules[$key] = $module;
    }

    public function addViewFrame($key, $viewModel)
    {
        $this->viewModels[$key] = $viewModel;
    }

    public function serve() {
        $request = self::createRequest();

        if (!array_key_exists($request->view, $this->viewModels)) {
            throw new \Exception("No view parameter delivered!");
        }

        $viewModel = $this->viewModels[$request->view];


        try {
            $viewModel->serve($request);
        } catch (\Exception $e) {
            if ($e instanceof \Prophecy\Exception\Call\UnexpectedCallException) {
                throw $e;
            }

            $this->logger->fatal($this->correlation_id,  $request);
        }
    }

    private static function createRequest() {
        $view = (isset($_GET['view'])) ? $_GET['view'] : 'index';
        $page = (isset($_GET['page'])) ? $_GET['page'] : 'index';

        $request = new Request();
        $request->view = $view;
        $request->method = $_SERVER['REQUEST_METHOD'];
        $request->page = $page;

        return $request;
    }

    public function getModule($key)
    {
        $request = self::createRequest();
        return $this->modules[$key]->serve($request);
    }
}
