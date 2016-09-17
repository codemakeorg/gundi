<?php

/**
 * Product: Gundi
 * Class:   Error
 * User:    Kalil uulu Bolot(bolotkalil@gmail.com)
 * Version: 0.0.1
 * Date:    15.11.2015
 * Time:    23:34
 */

namespace Core\Library\Error;

/**
 * Record and email/display errors or a custom error message.
 */
class Error
{
    /**
     * Holds an ARRAY of all the error messages we set
     *
     * @static
     * @var array
     */
    private static $_aErrors = [];

    /**
     * Holds a BOOL value if we should display the error messages or not
     *
     * @static
     * @var bool
     */
    private static $_bDisplay = true;

    /**
     * Holds a BOOL value if we should skip the error reporting or not
     *
     * @static
     * @var bool
     */
    private static $_bSkipError = false;


    /**
     * Displays the error message and directly creates a variable for the template engine
     *
     * @static
     * @param string $sMsg Error message you want to display on the current page the user is on.
     */
    public static function display($sMsg, $iErrCode = null, $sFormat = 'html', $aData = [])
    {
        Gundi()->Dispatch->dispatchController('\Module\Core\Component\Controller\DisplayError@index', array_merge(['sErrorMessage'=>$sMsg], $aData), $sFormat);
        if ($iErrCode !== null) {
            header(Gundi()->Url->getHeaderCode($iErrCode));
        }
        exit;
    }

    /**
     * Display a warning or error message
     *
     * @static
     * @param string $sMsg is the Error message
     * @param constant $sErrorCode is the valid constant. (eg. E_USER_WARNING will be a warning message and E_USER_ERROR will be a fatal error message)
     * @return bool If E_USER_ERROR is enabled we exit() the script, however if not we return FALSE
     */
    public static function trigger($sMsg, $sErrorCode = E_USER_WARNING)
    {
        trigger_error(strip_tags($sMsg), $sErrorCode);

        if ($sErrorCode == E_USER_ERROR)
        {
            exit;
        }

        return false;
    }

    /**
     * Set an error message that can be displayed at a later time
     *
     * @static
     * @param string $sMsg Error message you want to display
     * @return bool Always returns FALSE since we encountered an error
     */
    public static function set($sMsg)
    {
        self::$_aErrors[] = $sMsg;
        return false;
    }


    /**
     * Get all the reported errors thus far set by the method set()
     *
     * @see self::set()
     * @static
     * @return array Returns an ARRAY of error messages. If no errors it returns an empty ARRAY
     */
    public static function get()
    {
        return self::$_aErrors;
    }

    /**
     * Sets the display status of error reporting.
     *
     * @static
     * @param bool $bDisplay Sets the display status
     */
    public static function setDisplay($bDisplay)
    {
        self::$_bDisplay = $bDisplay;
    }

    /**
     * Gets the current display status of error reporting
     *
     * @static
     * @return array
     */
    public static function getDisplay()
    {
        return self::$_bDisplay;
    }

    /**
     * Returns if an error has accured up to this point. This is bassed on anything
     * set by the method set(). This is used with IF conditional statments to see if
     * we can continue with a routine or if an error has occured.
     *
     * Example usage:
     * <code>
     * if (Gundi()->Error::isPassed())
     * {
     * 		// Everything is okay do something here...
     * }
     * else
     * {
     * 		// Oh no there was an error. Display error messages here...
     * }
     * </code>
     *
     * @see self::set()
     * @static
     * @return boolean
     */
    public static function isPassed()
    {
        return (!count(self::$_aErrors) ? true : false);
    }

    /**
     * Reset the error messages. We do this automatically at the end of the
     * entire routine to display a page, however if you need to reset it earlier
     * it can be done with this method.
     *
     * @static
     *
     */
    public static function reset()
    {
        self::$_aErrors = [];
    }

    /**
     * If debug mode is enabled and you want to make sure to skip error reporting
     * you can use this method to force us to skip error reporting and then later
     * turn it back on. We mainly use this when dealing with 3rd party libraries
     * since we did not develop the code we are not fully aware of the coding standards
     * applied.
     *
     * @static
     * @param bool $bSkipError TRUE to skip error reporting and FALSE to turn error reporting back on.
     */
    public static function skip($bSkipError)
    {
        if ($bSkipError === true)
        {
            error_reporting(0);
        }
        else
        {
            error_reporting((GUNDI_DEBUG ? E_ALL | E_STRICT : 0));
        }
        self::$_bSkipError = $bSkipError;
    }

    /**
     * This method handles the output of the error message PHP returns. We extend the PHP error
     * reporting with providing more information on the error and where in the source code
     * it can be found.
     *
     * @static
     * @see set_error_handler
     * @param int $nErrNo The first parameter, errno, contains the level of the error raised, as an integer.
     * @param string $sErrMsg The second parameter, errstr, contains the error message, as a string.
     * @param string $sFileName The third parameter is optional, errfile, which contains the filename that the error was raised in, as a string.
     * @param int $nLinenum The fourth parameter is optional, errline, which contains the line number the error was raised at, as an integer.
     * @param array $aVars The fifth parameter is optional, errcontext, which is an array that points to the active symbol table at the point the error occurred. In other words, errcontext  will contain an array of every variable that existed in the scope the error was triggered in. User error handler must not modify error context.
     * @return bool We only return a BOOL FALSE if we need to skip error reporting, otherwise we echo the output.
     */
    public static function errorHandler($nErrNo, $sErrMsg, $sFileName, $nLinenum, $aVars = [])
    {
        if (defined('GUNDI_IS_API'))
        {
            echo serialize(array(
                    'error' => 'fatal',
                    'error_message' => $sErrMsg,
                    'return' => false
                )
            );

            exit;
        }

        if (self::$_bSkipError)
        {
            return false;
        }

        if ((defined('GUNDI_LOG_ERROR') && GUNDI_LOG_ERROR))
        {
            self::log($sErrMsg, $sFileName, $nLinenum);
        }

        if (!GUNDI_DEBUG && !error_reporting())
        {
            return false;
        }

        $aTypes = [
            1   =>  "Error",
            2   =>  "Warning",
            4   =>  "Parsing Error",
            8   =>  "Notice",
            16  =>  "Core Error",
            32  =>  "Core Warning",
            64  =>  "Compile Error",
            128 =>  "Compile Warning",
            256 =>  "User Error",
            512 =>  "User Warning",
            1024=>  "User Notice",
            2048=>  "PHP 5"
        ];

        $aColors = [
            1   =>  "#FF9999",
            2   =>  "#00FFFF",
            4   =>  "#FF9999",
            8   =>  "#99FF99",
            16  =>  "#FF9999",
            32  =>  "#00FFFF",
            64  =>  "#FF9999",
            128 =>  "#00FFFF",
            256 =>  "#FF9999",
            512 =>  "#00FFFF",
            1024=>  "#FF9999",
            2048=>  "#FF9999"
        ];

        if (substr(PHP_VERSION, 0, 1) < 5)
        {
            $iStart = 1;
        }
        else
        {
            $iStart = 0;
        }

        $bNoHtml = false;
        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') || (PHP_SAPI == 'cli'))
        {
            $bNoHtml = true;
        }

        $sErrMsg = str_replace(GUNDI_APP_DIR, '', $sErrMsg);
        if ($bNoHtml)
        {
            $sErr = "\n{$aTypes[$nErrNo]}: {$sErrMsg}\n";
        }
        else
        {
            if (!isset($aColors[$nErrNo]) || !isset($aTypes[$nErrNo]))
            {
                $nErrNo = 1;
            }
            $sErr = '<!-- Gundi Error Message --><br />
			<table border="0" cellspacing="0" cellpadding="2" style="font-family:Verdana;font-size:12px; border-color: #000000; border: 1px solid black; background:#fff;">
	        <tr>
	        	<td colspan="10" align="left" valig="top" style="background-color: ' . $aColors[$nErrNo] . '"><b>' . $aTypes[$nErrNo] . ':&nbsp;' . $sErrMsg . ' - ' . str_replace(GUNDI_APP_DIR, '', $sFileName) . ' (' . $nLinenum . ')</b></td></tr>';
        }

        $aFiles = debug_backtrace();

        for ($i=$iStart, $n=sizeof($aFiles); $i<$n; ++$i)
        {
            if (!isset($aFiles[$i]['file']))
            {
                continue;
            }

            $sArgs = '';
            if (isset($aFiles[$i]['args']))
            {
                $aArgs = array();
                $aArgs = array_merge($aFiles[$i]['args'], array());
                if ($aArgs and is_array($aArgs))
                {
                    foreach ($aArgs as $k=>$v)
                    {
                        if (is_numeric($v))
                        {
                            $aArgs[$k] = '<span style="color:#7700AA">'.$v.'</span>';
                        }
                        elseif(is_bool($v))
                        {
                            $aArgs[$k] = '<span style="color:#222288;">'.($v ? 'TRUE' : 'FALSE').'</span>';
                        }
                        elseif(is_null($v))
                        {
                            $aArgs[$k] = '<span style="color:#222288;">NULL</span>';
                        }
                        elseif(is_array($v))
                        {
                            $aArgs[$k] = 'Array('.count($v).')';
                        }
                        elseif (is_string($v) && ! (('"' == substr($v,0,1)) && ('"' == substr($v,-1))))
                        {
                            $aArgs[$k] = '<span style="color:#0000FF">"'.$v.'"</span>';
                        }
                        elseif(is_object($v))
                        {
                            unset($aArgs[$k]);
                            $aArgs[$k] = '{' . ucfirst(get_class($v)) . '}';
                        }
                    }
                }
                $sArgs = implode(', ', $aArgs);
            }

            $sFuncName = (isset($aFiles[$i]['class'])?$aFiles[$i]['class']:'').
                (isset($aFiles[$i]['type'])?$aFiles[$i]['type']:'').
                $aFiles[$i]['function'].'('.$sArgs.')';
            if ($iStart == $i)
            {
                $sFuncName = '<b>' . $sFuncName . '</b>';
            }
            $sFile = str_replace(GUNDI_APP_DIR, '', $aFiles[$i]['file']);

            if ($bNoHtml)
            {
                $sErr .= "{$i}\t{$sFile}\t" . (isset($aFiles[$i]['line']) ? $aFiles[$i]['line'] : '') . "\t" . strip_tags(str_replace(GUNDI_APP_DIR, '', $sFuncName)) . "\n";
            }
            else
            {
                $sErr .= '<tr><td bgcolor="#DDEEFF" align="right">'.$i.'&nbsp;</td>'.
                    '<td style="border-bottom:1px #000 solid;">' . $sFile . '&nbsp;:&nbsp;<b>'.(isset($aFiles[$i]['line']) ? $aFiles[$i]['line'] : '').'</b>&nbsp;&nbsp; </td>'.
                    '<td style="border-bottom:1px #000 solid; border-left:1px #000 solid;">' . str_replace(GUNDI_APP_DIR, '', $sFuncName) . '</td></tr>';
            }
        }

        if (!$bNoHtml)
        {
            $sErr .= '</table>';
        }

        echo $sErr;

        if (GUNDI_DEBUG)
        {
            exit;
        }
    }

    /**
     * Error messages can also be logged into a flat file on the server. The reason
     * for this certain AJAX request or API callbacks may be hard to find error reports
     * and by adding all error reports to a flat file it will help with debugging. This
     * is automatically used with our error handler.
     *
     * @see self::errorHandler()
     * @static
     * @param string $sMessage Error message to display
     * @return boolean
     */
    public static function log($sMessage)
    {
        $aCallbacks = debug_backtrace();
        $sBackTrace = '';
        foreach ($aCallbacks as $iKey=>$aCallback) {
            if (isset($aCallback['class']) && $aCallback['class'] == 'Error') {
                continue;
            }

            if (!isset($aCallback['file']) || !isset($aCallback['line'])) {
                continue;
            }
            $sBackTrace .= PHP_EOL.'  '.($iKey+1).')file:'.$aCallback['file'].'('.$aCallback['line'].');';
        }
        $sMessage = self::_escapeCdata($sMessage);
        $sRequest = var_export($_REQUEST, true);
        $sIp = $_SERVER['REMOTE_ADDR'];
        $sData = date('m/d/Y, H:i:s', GUNDI_TIME);
        $sErrorLog = <<<EOF
###################### {$sData} ######################
message:
  $sMessage;
backtrace:
    $sBackTrace;
request:
  $sRequest;
ip:
  $sIp

EOF;

        $hFile = fopen(GUNDI_ROOT . 'var' . GUNDI_DS . 'log' . GUNDI_DS . 'gundi_error_log_' . date('d_m_y', time()) . '.php', 'a');
        fwrite($hFile, PHP_EOL."{$sErrorLog}");
        return fclose($hFile);
    }

    /**
     * Removes any CDATA from a string.
     *
     * @static
     * @param string $sXml XML code to parse
     * @return string New string without CDATA
     */
    private static function _escapeCdata($sXml)
    {
        $sXml = preg_replace('#[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]#', '', $sXml);

        return str_replace(array('<![CDATA[', ']]>'), array('�![CDATA[', ']]�'), $sXml);
    }
}
