<?php
namespace Core\Library\Request;

use Core\Contract\Request\IRequest;
use Core\Library\Gundi;

class Request implements IRequest
{

    private $aExt = ['.html', '.xml', '.json'];

    /**
     * List of all the requests ($_GET, $_POST, $_FILES etc...)
     *
     * @var array
     */
    private
        $_aArgs = [];

    /**
     * List of requests being checked.
     *
     * @var array
     */
    private
        $_aName = [];

    /**
     * Last name being checked.
     *
     * @var string
     */
    private
        $_sName;

    /**
     * Class Constructor used to build the variable $this->_aArgs.
     *
     */
    public function __construct()
    {
        $this->getHttpMethod() === 'PUT' ? parse_str(file_get_contents('php://input', false, null, -1, $_SERVER['CONTENT_LENGTH']), $_PUT) : $_PUT = [];
        $this->_aArgs = $this->_trimData(array_merge($_GET, $_POST, $_FILES, $_PUT));
    }

    public function isPost()
    {
        return $this->getHttpMethod() === 'POST';
    }

    /**
     * Retrieve parameter value from request.
     *
     * @param string $sName name of argument
     * @param string $sCommand is any extra commands we need to execute
     * @return string parameter value
     */
    function get($sName = '', $mDef = '')
    {
        if ($this->_sName) {
            $sName = $this->_sName;
        }

        return (isset($this->_aArgs[$sName]) ? ((empty($this->_aArgs[$sName]) && isset($this->_aName[$sName])) ? true : $this->_aArgs[$sName]) : $mDef);
    }

    /**
     * Set a request manually.
     *
     * @param mixed $mName ARRAY include a name and value, STRING just the request name.
     * @param string $sValue If the 1st argument is a string this must be the request value.
     */
    public function set($mName, $sValue = null)
    {
        if (!is_array($mName) && $sValue !== null) {
            $mName = array($mName => $sValue);
        }

        foreach ($mName as $sKey => $sValue) {
            $this->_aArgs[$sKey] = $sValue;
        }
    }

    /**
     * Get all the requests.
     *
     * @return array
     */
    public function getRequests()
    {
        return (array)$this->_aArgs;
    }


    /**
     * Trims params and strip slashes if magic_quotes_gpc is set.
     *
     * @param mixed $mParam request params
     * @return mixed trimmed params.
     */
    private function _trimData($mParam)
    {
        if (is_array($mParam)) {
            return array_map(array(&$this, '_trimData'), $mParam);
        }
        if (get_magic_quotes_gpc()) {
            $mParam = stripslashes($mParam);
        }

        $mParam = trim($mParam);
        return $mParam;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->_aArgs[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->_aArgs[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->_aArgs[$offset]);
    }

    public function post($sName, $sType = null)
    {
        return $_POST[$sName];
    }

    public function getExt()
    {
        $sUrl = parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH);
        return substr($sUrl, strrpos($sUrl, '.') + 1);
    }

    public function getUri()
    {
        return str_replace($this->aExt, '', parse_url($_SERVER['PHP_SELF'], PHP_URL_PATH));
    }

    public function getHttpMethod()
    {
        return ((isset($_REQUEST['method']) && in_array($_REQUEST['method'], ['GET', 'POST', 'DELETE', 'PUT'])) ? $_REQUEST['method'] : $_SERVER['REQUEST_METHOD']);
    }

}