<?php
/**
 * @author Roman Gemini <roman_gemini@ukr.net>
 * @date 16.05.16
 * @time 22:15
 */

namespace Kote\Http\Response;


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
     */
    public function __construct($viewFileName, array $context = [])
    {
        $this->setViewFileName($viewFileName);
        $this->setContextData($context);
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

    public function renderContent()
    {
        $this->renderGenericView($this->getViewFullPath(), $this->getContextData());
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



