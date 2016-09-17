<?php
namespace Core\Library\Util;

class Url
{
    /**
     * List of headers
     *
     * @var array
     */
    protected $_aHeaders = array(
        100 => "HTTP/1.1 100 Continue",
        101 => "HTTP/1.1 101 Switching Protocols",
        200 => "HTTP/1.1 200 OK",
        201 => "HTTP/1.1 201 Created",
        202 => "HTTP/1.1 202 Accepted",
        203 => "HTTP/1.1 203 Non-Authoritative Information",
        204 => "HTTP/1.1 204 No Content",
        205 => "HTTP/1.1 205 Reset Content",
        206 => "HTTP/1.1 206 Partial Content",
        300 => "HTTP/1.1 300 Multiple Choices",
        301 => "HTTP/1.1 301 Moved Permanently",
        302 => "HTTP/1.1 302 Found",
        303 => "HTTP/1.1 303 See Other",
        304 => "HTTP/1.1 304 Not Modified",
        305 => "HTTP/1.1 305 Use Proxy",
        307 => "HTTP/1.1 307 Temporary Redirect",
        400 => "HTTP/1.1 400 Bad Request",
        401 => "HTTP/1.1 401 Unauthorized",
        402 => "HTTP/1.1 402 Payment Required",
        403 => "HTTP/1.1 403 Forbidden",
        404 => "HTTP/1.1 404 Not Found",
        405 => "HTTP/1.1 405 Method Not Allowed",
        406 => "HTTP/1.1 406 Not Acceptable",
        407 => "HTTP/1.1 407 Proxy Authentication Required",
        408 => "HTTP/1.1 408 Request Time-out",
        409 => "HTTP/1.1 409 Conflict",
        410 => "HTTP/1.1 410 Gone",
        411 => "HTTP/1.1 411 Length Required",
        412 => "HTTP/1.1 412 Precondition Failed",
        413 => "HTTP/1.1 413 Request Entity Too Large",
        414 => "HTTP/1.1 414 Request-URI Too Large",
        415 => "HTTP/1.1 415 Unsupported Media Type",
        416 => "HTTP/1.1 416 Requested range not satisfiable",
        417 => "HTTP/1.1 417 Expectation Failed",
        500 => "HTTP/1.1 500 Internal Server Error",
        501 => "HTTP/1.1 501 Not Implemented",
        502 => "HTTP/1.1 502 Bad Gateway",
        503 => "HTTP/1.1 503 Service Unavailable",
        504 => "HTTP/1.1 504 Gateway Time-out"
    );

    public function getHeaderCode($iCode)
    {
        if (isset($this->_aHeaders[$iCode])) {
            return $this->_aHeaders[$iCode];
        }
        return null;
    }

    /**
     * Encodes a URL string.
     *
     * @param string $sStr URL string.
     * @return string URL encoded string.
     */
    public function encode($sStr)
    {
        $sStr = serialize($sStr);

        if (function_exists('gzcompress')) {
            $sStr = gzcompress($sStr, 9);
        }

        return strtr(base64_encode(addslashes($sStr)), '+/=', '-_,');
    }

    /**
     * Decodes a URL string encoded with the method encode().
     *
     * @see self::encode()
     * @param string $sStr URL string to decode.
     * @return string Decoded URL string.
     */
    public function decode($sStr)
    {
        $sStr = stripslashes(base64_decode(strtr($sStr, '-_,', '+/=')));

        if (function_exists('gzuncompress')) {
            $sStr = gzuncompress($sStr);
        }

        return unserialize($sStr);
    }


    /**
     * Send the user to a new page. Works similar to PHP "header('Location: ...');".
     *
     * @param string $sUrl URL.
     */
    public function forward($sUrl, $iHeader = null)
    {
        $this->_send($sUrl, $iHeader);
        exit;
    }

    /**
     * Send a user to a new page using the URL method we use.
     *
     * @param string $sUrl Internal URL.
     * @param array $aParams ARRAY of params to include in the URL.
     * @param string $sMsg Optional message you can pass which will be displayed on the arrival page.
     */
    public function send($sUrl, $aParams = array(), $iHeader = null)
    {
        $this->_send((preg_match("/(http|https):\/\//i", $sUrl) ? $sUrl : $this->makeUrl($sUrl, $aParams)), $iHeader);
        exit;
    }


    /**
     * Get the domain name of the site.
     *
     * @return string
     */
    public function getDomain()
    {
        return Gundi()->config->getParam('core.path');
    }

    /**
     * Get the main url name of the site.
     *
     * @return string
     */
    public function getUrl()
    {
        return Gundi()->config->getParam('core.path') . GUNDI_INDEX_FILE;
    }

    /**
     * Get the full URL of the current page.
     *
     * @param bool $bNoPath TRUE to include the URL path, FALSE if not.
     * @return string URL.
     */
    public function getCurrentUrl()
    {
        return $this->makeUrl('current');
    }

    /**
     * Make an internal link.
     *
     * @param string $sUrl Internal link.
     * @param array $aParams ARRAY of params to include in the link.
     * @param bool $bFullPath Not using this argument any longer.
     * @return string Full URL.
     */
    public function makeUrl($sUrl, $aParams = [])
    {

        if (preg_match('/https?:\/\//i', $sUrl)) {

            return $sUrl;
        }

        // Make it an array if its not an array already (Used as shortcut)
        if (!is_array($aParams)) {
            $aParams = array($aParams);
        }

        if (!empty($aParams)) {
            $sUrl .= '?' . http_build_query($aParams);
        }

        return ($sUrl == 'current' ? Gundi()->config->getParam('core.protocol') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : $this->getUrl() . '/' . $sUrl);
    }

    /**
     * Send the user to a new location.
     *
     * @param string $sUrl Full URL.
     */
    private function _send($sUrl, $iHeader = null)
    {
        if (!empty($_POST['skip_page_redirect'])) {
            return;
        }

        // Clean buffer
        ob_clean();

        if (defined('GUNDI_IS_AJAX_PAGE') && GUNDI_IS_AJAX_PAGE) {
            echo 'window.location.href = \'' . $sUrl . '\';';
            exit;
        }

        if ($iHeader !== null && isset($this->_aHeaders[$iHeader])) {
            header($this->_aHeaders[$iHeader]);
        }

        // Send the user to the new location
        header('Location: ' . $sUrl);
    }

}

