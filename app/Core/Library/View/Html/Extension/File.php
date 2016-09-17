<?php
namespace Core\Library\View\Html\Extension;

use Core\Contract\View\IExtension;
use Core\Library\Setting\Setting;
use Core\Library\View\Html\View;

/**
 * File extension for set url path for files
 */
class File implements IExtension
{
    private $_oSetting;
    private $_aServers = [];
    private $_iServerId;
    private $_sPath;
    private $_sBasePath;

    public function __construct(Setting $oSetting)
    {
        $this->_oSetting = $oSetting;
        if (($aServers = $this->_oSetting->getParam('core.servers'))) {
            $this->_aServers = (array)$aServers;
        }
    }

    /**
     * Get path or url of file
     * @param $sType url|path
     */
    public function get($sType='url')
    {
        $sSrc = '';
        switch ($sType){
            case 'path':
                if (!empty($this->_iServerId) && isset($this->_aServers[$this->_iServerId])){
                    $sSrc .= trim($this->_aServers[$this->_iServerId], '/\\').'/'.($this->_sBasePath?$this->_sBasePath.'/':'').$this->_sPath;
                }else{
                    $sSrc .= str_replace('/', '\\', $this->_oSetting->getParam('core.app_dir').($this->_sBasePath?$this->_sBasePath.GUNDI_DS:'').$this->_sPath);
                }
                break;
            default:
                if (!empty($this->_iServerId) && isset($this->_aServers[$this->_iServerId])){
                    $sSrc .= trim($this->_aServers[$this->_iServerId], '/\\').'/'.($this->_sBasePath?$this->_sBasePath.'/':'').$this->_sPath;
                }else{
                    $sSrc .= trim($this->_oSetting->getParam('core.path'), '/\\').'/'.($this->_sBasePath?$this->_sBasePath.'/':'').$this->_sPath;
                }
        }
        $this->_sPath = '';
        $this->_sBasePath = '';
        $this->_iServerId = 0;
        return $sSrc;
    }

    /**
     * Set path of file and module name
     * Example Usage (PHP)
     * <code>
     * $this->from('path/to/location')->get();
     * </code>
     * @param $mPath
     */
    public function from($sBasePath='')
    {
        $this->_sBasePath = trim($sBasePath, '/\\');
        return $this;
    }

    /**
     * Set server location of file
     * @param 0 $iId
     */
    public function server($iId=0)
    {
        $this->_iServerId = $iId;
        return $this;
    }

    /**
     * Return instance of file
     * Example Usage (PHP)
     * <code>
     * $this->file('path/to/file'|['path/to/file_%s'=>'sizeOfFile'])->get();
     * </code>
     * @param $sBasePath
     * @return $this
     */
    public function file($mPath)
    {
        if (is_array($mPath)){
            $this->_sPath = sprintf(key($mPath), current($mPath));
        }else{
            $this->_sPath = $mPath;
        }

        $this->_sPath = trim($this->_sPath, '/\\');
        return $this;
    }
    /**
     * @param View $oView
     */
    public function register(View &$oView)
    {
        $oView->registerFunc('file', [$this, 'file']);
    }
}
