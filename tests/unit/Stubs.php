<?php
namespace Tests\unit\Stubs;


use Core\Contract\Request\IRequest;
use Core\Library\Error\Error;
use Core\Library\Gundi\Gundi;
use Core\Library\Theme\Theme;
use Core\Library\View\JsonView;

class StubRequest extends \ArrayObject implements IRequest
{

    public static $sExt = 'json';
    public static $sURI = '/';
    public static $sMethod = 'GET';

    public function get($sName, $sType = null)
    {
        return $this[$sName];
    }

    public function post($sName, $sType = null)
    {
        return $this[$sName];
    }

    public function getExt()
    {
        return self::$sExt;
    }

    public function getUri()
    {
        return self::$sURI;
    }

    public function getHttpMethod()
    {
        return self::$sMethod;
    }

    public function isPost()
    {
        return self::$sMethod == 'POST';
    }
}

class StubTheme extends Theme
{
    public function __construct()
    {
    }
}

class MockGundi extends Gundi
{
    public $version = '1.0.0';
    public $aService = [];

    public function make($abstract, array $parameters = [])
    {
        if (isset($this->aService[$abstract])) {
            return $this->aService[$abstract];
        }

        return parent::make($abstract, $parameters);
    }

    public function getVersion()
    {
        return $this->version;
    }
}


class StubError extends Error
{
    static public $oView = null;

    public static function display($sMsg, $iErrCode = null, $sFormat = 'html', $aData = [])
    {
        self::getView()->assign($aData);
        echo $sMsg;
    }

    private static function getView()
    {
        if (is_null(self::$oView)){
            self::$oView = new JsonView();
        }
        return self::$oView;
    }

}