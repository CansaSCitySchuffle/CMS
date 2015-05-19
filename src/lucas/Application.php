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

    public function serve($view, $action) {
        if (!array_key_exists($view, $this->viewModels)) {
            throw new \Exception("foo");
        }

        $viewModel = $this->viewModels[$view];

        try {
            $viewModel->serve($action);
        } catch (\Exception $e) {
            $this->logger->fatal($this->correlation_id, 'guest', null);
        }
    }
}
