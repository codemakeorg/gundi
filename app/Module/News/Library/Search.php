<?php
/**
 * To demonstrate limited to the following criteria:
 * 1. like
 * 2. equal
 * 3. notEqual
 */
namespace Module\News\Library;

class Search
{
    const CRITERIA_DELIMITER = '__';
    const OPERATOR_POSITION = 1;
    const COLUMN_NAME_POSITION = 0;

    /**
     * We can extend parsing critation
     * @var array
     */
    protected $aExtends = [];

    /**
     * Parsing and normalize criteria for query builder
     * Example
     *  input:
     *    ['name:like' => 'test', 'email:equal' => 'test@test.test']
     *  output:
     *   [
     *      ['name', 'like', '%test%'],
     *      ['email', '=', 'test@test.test'],
     *    ]
     * @param array $aCriteria
     * @return array
     */
    public function normalizeCriteria($aCriteria = [])
    {
        $aResult = [];
        foreach ($aCriteria as $sColumnOperation => &$mValue) {
            $sColumnOperation = explode(self::CRITERIA_DELIMITER, $sColumnOperation);
            $sOperator = isset($sColumnOperation[self::OPERATOR_POSITION])
                ? $sColumnOperation[self::OPERATOR_POSITION]
                : 'equal';
            $sColumnName = $sColumnOperation[self::COLUMN_NAME_POSITION];
            $aResult[] = call_user_func_array([$this, $sOperator], [$sColumnName, $mValue]);
        }
        return $aResult;
    }

    /**
     * @param $sColumnName
     * @param $sValue
     * @return array
     */
    protected function like($sColumnName, $sValue)
    {
        return [$sColumnName, 'like', '%' . $sValue . '%'];
    }

    /**
     * @param $sColumnName
     * @param $sValue
     * @return array
     */
    protected function equal($sColumnName, $sValue)
    {
        return [$sColumnName, '=', $sValue];
    }

    /**
     * @param $sColumnName
     * @param $sValue
     * @return array
     */
    protected function notEqual($sColumnName, $sValue)
    {
        return [$sColumnName, '!=', $sValue];
    }

    /**
     * Extend parser
     * @param $sName
     * @param \Closure $closure - must return array
     * @return $this
     */
    public function extend($sName, \Closure $closure)
    {
        $this->aExtends[$sName] = $closure;
        return $this;
    }

    public function __call($sName, $aArguments)
    {
        if (!isset($this->aExtends[$sName]) || !($this->aExtends[$sName] instanceof \Closure)) {
            throw new \InvalidArgumentException('Parsing method "' . $sName . '" undefined');
        }
        return call_user_func_array($this->aExtends[$sName], $aArguments);
    }

}