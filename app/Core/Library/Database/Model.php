<?php
namespace Core\Library\Database;

use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent
{
    public function toArray()
    {
        $aArray = parent::toArray();
        foreach ($this->getMutatedAttributes() as $sKey)
        {
            if ( ! array_key_exists($sKey, $aArray)) {
                $aArray[$sKey] = $this->{$sKey};
            }
        }
        return $aArray;
    }
}
