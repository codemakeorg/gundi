<?php
namespace Core\Library\Form\Field;


use Core\Contract\Field\IType;
use Core\Library\Form\Exception\RequiredArgumentException;
use Core\Library\Theme\Theme;
use Core\Library\Validator\ValidatorTrait;
use Core\Library\View\AbstractView;

abstract class AbstractType implements IType, \JsonSerializable
{
    use ValidatorTrait;

    protected $rules = [];
    protected $validationMessages = [];
    protected $aInfo = [
        'template' => 'Core:Type/string',
    ];

    protected $bHasError = false;

    /**
     * @var AbstractView
     */
    protected $oView;

    /**
     * @var Theme
     */
    protected $oTheme;

    protected $oValidator = null;

    /**
     * AbstractType constructor.
     * <code>
     *   $oText = new TextType(
     * [
     *      'name' => 'username',
     *      'Caption' => 'Your Name',
     *      'required' => true,
     * ]
     * );
     * </code>
     * @param array $aData - data of type
     */
    public function __construct(array $aData)
    {
        if (!isset($aData['name'])) {
            throw  new RequiredArgumentException('Required element "name" in argument aData');
        }

        if (!isset($aData['title'])) {
            throw  new RequiredArgumentException('Required element "title" in argument aData');
        }

        if (isset($aData['rules'])) {
            $this->rules = [
                $aData['name'] => is_array($aData['rules']) ? implode('|', $aData['rules']) : $aData['rules'],
            ];
        }

        if (isset($aData['validation_messages'])) {
            $this->validationMessages = $aData['validation_messages'];
        }

        $this->aInfo = array_merge($this->aInfo, $aData);
    }

    public function setHasError($bHas)
    {
        $this->bHasError = $bHas;
    }

    public function hasError()
    {
        return $this->bHasError;
    }

    public function setValue($mValue)
    {
        $this->aInfo['value'] = $mValue;
        return $this;
    }

    /**
     * Make a Validator instance for a given ruleset.
     *
     * @param  array $aRules
     * @return \Illuminate\Validation\Factory
     */
    protected function makeValidator($aRules = [])
    {
        if ($this->getInjectUniqueIdentifier()) {
            $aRules = $this->injectUniqueIdentifierToRules($aRules);
        }

        $aMessages = $this->getValidationMessages();

        $oValidator = $this->getValidator()->make([$this->aInfo['name'] => $this->getValue()], $aRules, $aMessages);

        if ($this->getValidationAttributeNames()) {
            $oValidator->setAttributeNames($this->getValidationAttributeNames());
        }

        return $oValidator;
    }

    /**
     * @return void
     */
    protected function assignVars()
    {
        $this->oView->assign($this->aInfo);
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->aInfo['value']);
    }

    public function render($sTemplate = null)
    {
        $sTpl = is_null($sTemplate) ? $this->aInfo['template'] : $sTemplate;
        $this->oTheme->setTemplate($sTpl);
        $this->assignVars();
        $this->oView->assign('errors', $this->bHasError ? $this->getErrors()->getMessages()[$this->aInfo['name']] : null);
        $this->oView->assign('hasError', $this->bHasError);
        return $this->oView->render();
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return isset($this->aInfo['value']) ? $this->aInfo['value'] : null;
    }

    /**
     * @return AbstractView
     */
    public function getView()
    {
        return $this->oView;
    }

    /**
     * @param AbstractView $oView
     * @return  $this
     */
    public function setView(AbstractView $oView)
    {
        $this->oView = $oView;
        return $this;
    }

    /**
     * @return Theme
     */
    public function getTheme()
    {
        return $this->oTheme;
    }

    /**
     * @param Theme $oTheme
     * @return  $this
     */
    public function setTheme($oTheme)
    {
        $this->oTheme = $oTheme;
        return $this;
    }


    public function __toString()
    {
        return $this->render();
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return $this->aInfo;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return $this->getInfo();
    }
}