<?php
namespace Core\Library\View\Html\Extension;

use Core\Contract\View\IExtension;
use Core\Library\View\Html\View;

/**
 * Collect and output css and js link tags.
 */
class Asset implements IExtension
{
    /**
     * Collection of assets(css, js, meta tags)
     * @var array
     */
    private $_aAssets = [];
    /**
     * @var array Asset template
     */
    protected $_aTemplates = [
        'js' => '<script src="%s" type="text/javascript"></script>',
        'css' => '<link href="%s" rel="stylesheet" type="text/css">'
    ];

    /**
     * Last collect type
     * @var string
     */
    private $_sLastType;

    /**
     * @param $sFile
     * @param $sType
     * @return string
     */
    protected function toStr($sFile, $sType)
    {
        $sTemplate = $this->_aTemplates[trim($sType)];
        return sprintf($sTemplate, $sFile);
    }

    /**
     * Collection feature of class
     *
     * @param $sResource
     * @param $sType
     * @param null $iOrder
     */
    private function collect($sResource, $sType, $iOrder = null)
    {
        $this->_sLastType = $sType;
        if ($iOrder === null)
            return $this->_aAssets[$sType][] = $sResource;

        if (isset($this->_aAssets[$sType][$iOrder])) {
            if ($iOrder === 0)
                return array_unshift($this->_aAssets[$sType], $sResource);

            $aTmp = array_slice($this->_aAssets[$sType], $iOrder, count($this->_aAssets[$sType]));
            array_splice($this->_aAssets[$sType], $iOrder, count($this->_aAssets[$sType]));
            $this->_aAssets[$sType][] = $sResource;
            foreach ($aTmp as $v)
                $this->_aAssets[$sType][] = $v;

        } else {
            return $this->_aAssets[$sType][$iOrder] = $sResource;
        }
    }

    /**
     * Add files(js, css)
     * @param $mFiles
     * @param $iOrder default increment
     */
    public function addStatic($mFiles, $iOrder = null)
    {
        $iInputOrder = $iOrder;
        $mFiles = (!is_array($mFiles) ? (array)$mFiles : $mFiles);
        foreach ($mFiles as $sFile => $sValue) {
            if (!preg_match('#^([0-9]{1,})$#', $sFile)) {
                $aParts = explode('\\', $sFile);
                $aInfo = pathinfo($aParts[sizeof($aParts) - 1]);
                if (isset($aInfo['extension'])) {
                    $sPath = Gundi()->config->getParam('core.path') . $sValue;
                    $sPath = $sPath . implode('/', $aParts);
                    $sResource = $this->toStr($sPath, $aInfo['extension']);
                    $iOrder = $aInfo['extension'] == $this->_sLastType ? $iOrder : $iInputOrder;
                    $this->collect($sResource, $aInfo['extension'], $iOrder);
                    $iOrder = $iOrder !== null ? $iOrder + 1 : null;
                }
            } else {
                $aInfo = pathinfo($sValue);
                if (isset($aInfo['extension'])) {
                    $sResource = $this->toStr($sValue, $aInfo['extension']);
                    $iOrder = $aInfo['extension'] == $this->_sLastType ? $iOrder : $iInputOrder;
                    $this->collect($sResource, $aInfo['extension'], $iOrder);
                    $iOrder = $iOrder !== null ? $iOrder + 1 : null;
                }
            }
        }
    }

    /**
     * add tag(meta etc.)
     * @param $mTags
     * @param null $iOrder
     * @throws \ErrorException
     */
    public function addTag($mTags, $iOrder = null)
    {
        $mTags = (!is_array($mTags) ? (array)$mTags : $mTags);
        foreach ($mTags as $sTag => $sTagName) {
            if (preg_match('#^([0-9]{1,})$#', $sTag))
                throw new \LogicException('You have not added namespace for tag');
            $this->collect($sTag, $sTagName, $iOrder++);
        }
    }


    public function css()
    {
        return implode('', $this->getAssets('css'));
    }

    public function js()
    {
        return implode('', $this->getAssets('js'));
    }

    public function getAssets($sType)
    {
        if (!isset($this->_aAssets[$sType]))
            return [];
        return $this->_aAssets[$sType];
    }

    /**
     * @param View $oView
     */
    public function register(View &$oView)
    {
        $oView->registerFunc('css', [$this, 'css']);
        $oView->registerFunc('js', [$this, 'js']);
        $oView->registerFunc('addStatic', [$this, 'addStatic']);
    }
}
