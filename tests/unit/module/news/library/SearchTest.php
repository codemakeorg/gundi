<?php

namespace tests\unit\module\news\library;

use Module\News\Library\Search;

class SearchTest extends \Gundi_Framework_TestCase
{

    public function testNormalizeTest()
    {
        $oSearch = new Search();

        $aActualCriteria = $oSearch->normalizeCriteria([
            'name__like' => 'test_name',
            'name__equal' => 'test',
            'name__notEqual' => 'test',
            'name' => 'test', //default operator is equal
        ]);

        $aExpectedCriteria = [
            ['name', 'like', '%test_name%'],
            ['name', '=', 'test'],
            ['name', '!=', 'test'],
            ['name', '=', 'test'],
        ];
        $this->assertEquals($aExpectedCriteria, $aActualCriteria);

    }

    public function testExtend()
    {
        $oSearch = new Search();
        $oSearch->extend('testoperator', function($sName, $mValue){
           return [$sName, 'testoper', $mValue];
        });
        $aActualCriteria = $oSearch->normalizeCriteria([
            'name__testoperator' => 'test_name',
        ]);

        $aExpectedCriteria = [
            ['name', 'testoper', 'test_name'],
        ];
        $this->assertEquals($aExpectedCriteria, $aActualCriteria);
    }
}