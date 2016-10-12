<?php

namespace Nerd\Framework\Http\Response;

use Nerd\Framework\Http\IO\OutputContract;

class GenericViewResponse extends Response
{
    /**
     * @var string
     */
    private $viewFileName;

    /**
     * @var array
     */
    private $contextData = [];

    /**
     * @var string
     */
    private static $viewDirectoryPrefix = "";

    /**
     * @param $viewFileName
     * @param array $context
     * @param int $statusCode
     */
    public function __construct($viewFileName, array $context = [], $statusCode = StatusCode::HTTP_OK)
    {
        $this->setViewFileName($viewFileName);
        $this->setContextData($context);
        $this->setStatusCode($statusCode);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function bindVariable($key, $value)
    {
        $this->contextData[$key] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public static function getViewDirectoryPrefix()
    {
        return self::$viewDirectoryPrefix;
    }

    /**
     * @param string $viewDirectoryPrefix
     */
    public static function setViewDirectoryPrefix($viewDirectoryPrefix)
    {
        self::$viewDirectoryPrefix = $viewDirectoryPrefix;
    }

    /**
     * @return mixed
     */
    public function getViewFileName()
    {
        return $this->viewFileName;
    }

    /**
     * @param mixed $viewFileName
     * @return $this
     */
    public function setViewFileName($viewFileName)
    {
        $this->viewFileName = $viewFileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewFullPath()
    {
        return rtrim(self::getViewDirectoryPrefix(), "/") . "/" . $this->getViewFileName();
    }

    /**
     * @return mixed
     */
    public function getContextData()
    {
        return $this->contextData;
    }

    /**
     * @param mixed $contextData
     * @return $this
     */
    public function setContextData($contextData)
    {
        $this->contextData = $contextData;

        return $this;
    }

    public function renderContent(OutputContract $output)
    {
        ob_start();
        $this->renderGenericView(
            $this->getViewFullPath(),
            $this->getContextData()
        );
        $output->sendData(ob_get_clean());
    }

    /**
     * @param $view
     * @param array $context
     */
    private function renderGenericView($view, array $context)
    {
        extract($context);
        unset($context);
        /** @noinspection PhpIncludeInspection */
        include $view;
    }
}
