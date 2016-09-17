<?php
namespace Core\Library\Setting;

use JsonSerializable;
use Core\Contract\Assist\IJsonAble;
use Core\Contract\Assist\IArrayAble;

class Setting implements \ArrayAccess, IArrayAble, IJsonAble, JsonSerializable
{
    /**
     * List of all the settings.
     *
     * @var array
     */
    private $_aParams = [];

    private $_aDefaults = [
        'core.session_prefix' => 'GUNDI_',
        'core.title_delim' => '&raquo;',
        'core.site_title' => 'Gundi',
        'core.branding' => false,
        'core.default_lang_code' => 'en',
        'core.theme_layout' => 'index',
        'core.default_theme_name' => 'default',
        'core.cookie_path' => '/',
        'core.cookie_domain' => '',
        'core.site_copyright' => 'MeBo SoftWare ©',
        'db' => array(
            'host' => 'localhost',
            'user' => '',
            'pass' => '',
            'name' => '',
            'driver' => 'mongodb'
        ),
        'core.default_time_zone_offset' => 'Asia/Bishkek',
        'core.site_email' => 'site@site.com'
    ];


    /**
     * Create a new fluent container instance.
     *
     * @internal param array|object $attributes
     */
    public function __construct()
    {

        if (file_exists(GUNDI_DIR_SETTING . 'Common.php')) {

            $_CONF = [];
            require(GUNDI_DIR_SETTING . 'Common.php');
            $this->_aParams =& $_CONF;

        }else{
            throw new \LogicException('Common configuration file not found in :'.GUNDI_DIR_SETTING);
        }

    }

    /**
     * Creates a new setting.
     *
     * @param array|string $mParam ARRAY of settings and values.
     * @param string $mValue Value of setting if the 1st argument is a string.
     */
    public function setParam($mParam, $mValue = null)
    {
        if (is_string($mParam))
        {
            $this->_aParams[$mParam] = $mValue;
        }
        else
        {
            foreach ($mParam as $mKey => $mValue)
            {
                $this->_aParams[$mKey] = $mValue;
            }
        }
    }

    /**
     * Get a setting and its value.
     *
     * @param mixed $mVar STRING name of the setting or ARRAY name of the setting..
     * @return mixed Returns the value of the setting, which can be a STRING, ARRAY, BOOL or INT.
     */
    public function getParam($mVar)
    {
        if (is_array($mVar)) {

            $sParam = (isset($this->_aParams[$mVar[0]][$mVar[1]]) ? $this->_aParams[$mVar[0]][$mVar[1]] : (isset($this->_aDefaults[$mVar[0]][$mVar[1]]) ? $this->_aDefaults[$mVar[0]][$mVar[1]] : (trigger_error('Missing Param: ' . $mVar[0] . '][' . $mVar[1], E_USER_ERROR))));

        } else {

            $sParam = (isset($this->_aParams[$mVar]) ? $this->_aParams[$mVar] : (isset($this->_aDefaults[$mVar]) ? $this->_aDefaults[$mVar] : (trigger_error('Missing Param: ' . $mVar, E_USER_ERROR))));

        }

        return $sParam;
    }

    /**
     * Checks to see if a setting exists or not.
     *
     * @param string $mVar Name of the setting.
     * @return bool TRUE it exists, FALSE if it does not.
     */
    public function isParam($mVar)
    {
        return (isset($this->_aParams[$mVar]) ? true : false);
    }

    /**
     * Unset an param via key.
     *
     * @param  string  $sKey
     * @return void
     */
    public function unsetParam($sKey)
    {
        unset($this->_aParams[$sKey]);
    }


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_aParams;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $iOptions
     * @return string
     */
    public function toJson($iOptions = 0)
    {
        return json_encode($this->jsonSerialize(), $iOptions);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->toArray();
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
        return $this->isParam($offset);
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
        return $this->getParam($offset);
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
        $this->setParam($offset, $value);
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
        unset($this->_aParams[$offset]);
    }

}

