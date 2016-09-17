<?php

namespace Core\Library\Session;

/**
 * Prefix sessions with useful methods.
 */
class Session
{
    public function __construct()
    {
        //parent::__construct($_COOKIE);
    }

    /**
     * Determine if session has started.
     *
     * @var boolean
     */
    private $_bSessionStarted = false;

    /**
     * Check is session started
     *
     * @return bool
     */

    public function isStarted()
    {
        return $this->_bSessionStarted;
    }

    /**
     * if session has not started, start sessions
     */
    public function start()
    {
        if ($this->_bSessionStarted == false) {
            session_start();
            $this->_bSessionStarted = true;
        }
    }

    /**
     * Add value to a session.
     *
     * @param string $mKey name the data to save
     * @param string $sValue the data to save
     */
    public function set($mKey, $sValue = false)
    {
        /**
         * Check whether session is set in array or not
         * If array then set all session key-values in foreach loop
         */
        if (is_array($mKey) && $sValue === false) {
            foreach ($mKey as $sName => $sValue) {
                $_SESSION[Gundi()->config->getParam('core.session_prefix') . $sName] = $sValue;
            }
        } else {
            $_SESSION[Gundi()->config->getParam('core.session_prefix') . $mKey] = $sValue;
        }
    }

    /**
     * Extract item from session then delete from the session, finally return the item.
     *
     * @param  string $sKey item to extract
     *
     * @return mixed|null      return item or null when key does not exists
     */
    public function pull($sKey)
    {
        if (isset($_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey])) {
            $value = $_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey];
            unset($_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey]);
            return $value;
        }
        return null;
    }

    /**
     * Get item from session.
     *
     * @param  string $sKey item to look for in session
     * @param  boolean $sSecondKey if used then use as a second key
     *
     * @return mixed|null         returns the key value, or null if key doesn't exists
     */
    public function get($sKey, $sSecondKey = false)
    {
        if ($sSecondKey == true) {
            if (isset($_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey][$sSecondKey])) {
                return $_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey][$sSecondKey];
            }
        } else {
            if (isset($_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey])) {
                return $_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey];
            }
        }
        return null;
    }

    /**
     * id
     *
     * @return string with the session id.
     */
    public function id()
    {
        return session_id();
    }


    /**
     * Empties and destroys the session.
     *
     * @param  string $sKey - session name to destroy
     * @param  boolean $bPrefix - if set to true clear all sessions for current GUNDI_SESSION_PREFIX
     *
     */
    public function destroy($sKey = '', $bPrefix = false)
    {
        /** only run if session has started */
        if ($this->_bSessionStarted == true) {
            /** if key is empty and $prefix is false */
            if ($sKey == '' && $bPrefix == false) {
                session_unset();
                session_destroy();
            } elseif ($bPrefix == true) {
                /** clear all session for set session_prefix */
                foreach ($_SESSION as $sKey => $sValue) {
                    if (strpos($sKey, Gundi()->config->getParam('core.session_prefix')) === 0) {
                        unset($_SESSION[$sKey]);
                    }
                }
            } else {
                /** clear specified session key */
                unset($_SESSION[Gundi()->config->getParam('core.session_prefix') . $sKey]);
            }
        }
    }

}
