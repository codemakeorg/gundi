<?php
namespace Core\Library\Router;

use Core\Contract\Request\IRequest;

/**
 * Router class will load requested Controller / closure based on url.
 */
class Router
{
    /**
     * Base url of site
     * @var string
     */
    private $sBasePath = '';
    /**
     * Array of routes
     *
     * @var array $aRoutes
     */
    protected $aRoutes = [];
    /**
     * Array of methods
     *
     * @var array $aMethods
     */
    protected $aMethods = ['GET', 'POST', 'DELETE', 'PUT'];

    /**
     * Array of callbacks
     *
     * @var array $aCallbacks
     */
    protected $aCallbacks = [];

    /**
     * Before function
     *
     * @var array $_aBefore
     */

    protected $_aBefore = [];

    /**
     * Set an error callback
     *
     * @var null $mErrorCallback
     */
    private $mErrorCallback;
    /**
     * @var \Core\Contract\Request\IRequest
     */
    private $oRequest;

    private $sCurrentRoute;
    private $aCurrentVars;

    /** Set route patterns */
    protected $aPatterns = [
        ':any' => '[^/]+',
        ':id' => '(?!new)[^/]+',
        ':num' => '-?[0-9]+',
        ':all' => '.*',
        ':hex' => '[[:xdigit:]]+',
        ':uuidV4' => '\w{8}-\w{4}-\w{4}-\w{4}-\w{12}'
    ];


    public function __construct(IRequest $oRequest)
    {
        $this->oRequest = $oRequest;
    }

    protected function normalizeUri($sUri)
    {
        return $this->sBasePath . (!empty($sUri) ? '/' . $sUri : '');
    }

    private function normalizeCallback($sCallback)
    {
        return is_callable($sCallback) ? $sCallback : str_replace('/', '\\', $sCallback);
    }

    public function add($sUri, $sMethod, $sCallback)
    {
        $this->aRoutes[] = $this->normalizeUri($sUri);
        $this->aCallbacks[] = [$sMethod => $this->normalizeCallback($sCallback)];
        return $this;
    }

    public function get($sUri, $sCallback)
    {
        $this->add($sUri, 'GET', $sCallback);
        return $this;
    }

    public function post($sUri, $sCallback)
    {
        $this->add($sUri, 'POST', $sCallback);
        return $this;
    }

    public function delete($sUri, $sCallback)
    {
        $this->add($sUri, 'DELETE', $sCallback);
        return $this;
    }

    public function put($sUri, $sCallback)
    {
        $this->add($sUri, 'PUT', $sCallback);
        return $this;
    }

    public function match($sUri, $sCallback, $aIncludes = [])
    {
        if (!empty($aIncludes)) {
            foreach ($aIncludes as &$sInclude) {
                $this->add($sUri, $sInclude, $sCallback);
            }
        }
        return $this;
    }

    public function any($sUri, $sCallback)
    {
        foreach ($this->aMethods as &$sInclude) {
            $this->add($sUri, $sInclude, $sCallback);
        }
        return $this;
    }

    /**
     * Defines callback if route is not found.
     *
     */
    public function error($mCallback)
    {
        $this->mErrorCallback = $mCallback;
        return $this;
    }

    private $mCallback = null;
    private $aVars = null;

    public function setCallback($mCallback)
    {
        $this->mCallback = $mCallback;
    }

    public function setVars($aVars = [])
    {
        $this->aVars = $aVars;
    }

    private function getHandlerDetail($mCallback, $aMatched = [])
    {
        $mCallback = is_null($this->mCallback) ? $mCallback : $this->mCallback;
        $aMatched = is_null($this->aVars) ? $aMatched : $this->aVars;

        if ($mCallback instanceof \Closure) {
            return ['function' => $mCallback, 'vars' => $aMatched];
        }

        $aLast = explode('/', $mCallback);
        $aLast = end($aLast);

        $aSegments = explode('@', $aLast);

        $sController = $aSegments[0];
        $aMethod = $aSegments[1];
        return [
            'Controller' => $sController,
            'method' => $aMethod,
            'vars' => $aMatched,
        ];
    }

    public function getRouteExt()
    {
        return $this->oRequest->getExt();
    }

    /**
     * Get the callback for the given request.
     */
    public function getHandlerData()
    {
        $sUri = $this->oRequest->getUri();
        $sMethod = $this->oRequest->getHttpMethod();
        $aSearches = array_keys($this->aPatterns);
        $aReplaces = array_values($this->aPatterns);

        $this->aRoutes = str_replace('//', '/', $this->aRoutes);
        if (sizeof($this->_aBefore) > 0) {
            foreach ($this->_aBefore as $sKey => &$mValue) {
                if (preg_match('#^' . $sKey . '$#', $sUri, $aMatched)) {
                    if (isset($mValue[$sMethod])) {
                        array_shift($aMatched);
                        call_user_func_array($mValue[$sMethod], $aMatched);
                    }
                }
            }
        }

        $aRoutes = $this->aRoutes;
        foreach ($aRoutes as $iPos => $sRoute) {
            $sCurRoute = str_replace(['//', '\\'], '/', $sRoute);

            if (strpos($sCurRoute, ':') !== false) {
                $sRoute = str_replace($aSearches, $aReplaces, $sCurRoute);
            }
            if (preg_match('#^' . $sRoute . '$#', $sUri, $aMatched)) {

                if (isset($this->aCallbacks[$iPos][$sMethod])) {
                    //remove $matched[0] as [1] is the first parameter.
                    array_shift($aMatched);

                    $this->sCurrentRoute = $sCurRoute;
                    $this->aCurrentVars = $aMatched;

                    return $this->getHandlerDetail($this->aCallbacks[$iPos][$sMethod], $aMatched);
                }

            }
        }

        return $this->getHandlerDetail($this->mErrorCallback, []);
    }

    /**
     * Set before handler code.
     * @param $sUri
     * @param $mCallback
     */
    public function before($sUri, $mCallback, $aMethods = ['GET', 'POST', 'PUT', 'DELETE'])
    {
        $aSearches = array_keys($this->aPatterns);
        $aReplaces = array_values($this->aPatterns);
        $sUri = $this->normalizeUri($sUri);
        $sUri = str_replace($aSearches, $aReplaces, $sUri);
        $sUri = str_replace('//', '/', $sUri);
        $sUri = str_replace('*', '.{0,}', $sUri);
        foreach ($aMethods as $sMethod) {
            $this->_aBefore[$sUri][$sMethod] = $mCallback;
        }
    }

    /**
     * @param $sRoute
     * @param $sClass
     * @param array $aExclude
     */
    public function resource($sRoute, $sClass, $aExclude = [])
    {
        $aActions = [
            'index' => ['GET', ''],
            'show' => ['GET', '/(:id)'],
            'add' => ['GET', '/new'],
            'edit' => ['GET', '/(:id)/edit'],
            'update' => ['PUT', '/(:id)'],
            'delete' => ['DELETE', '/(:id)'],
            'create' => ['POST', '']
        ];

        $aActions = array_diff_key($aActions, array_flip($aExclude));

        foreach ($aActions as $sAction => $aValue) {
            list($sMethod, $sUri) = $aValue;
            $this->add($sRoute . $sUri, $sMethod, $sClass . '@' . $sAction);
        }
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->sBasePath;
    }

    /**
     * Base url of site.
     * <code>
     * $oRouter->setBaseUrl('');
     * </code>
     * @param string $sBaseUrl
     * @return  $this
     */
    public function setBasePath($sBaseUrl)
    {
        $this->sBasePath = $sBaseUrl;
        return $this;
    }

    /**
     * @return IRequest
     */
    public function getRequest()
    {
        return $this->oRequest;
    }

    /**
     * @param $oRequest
     * @return $this
     */
    public function setRequest($oRequest)
    {
        $this->oRequest = $oRequest;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentRoute()
    {
        return $this->sCurrentRoute;
    }

    /**
     * @return array
     */
    public function getCurrentVars()
    {
        return $this->aCurrentVars;
    }
    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->aRoutes;
    }

}
