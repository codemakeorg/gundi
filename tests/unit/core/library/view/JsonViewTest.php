<?php

use Core\Library\Token\Token;
class JsonViewTest extends \Gundi_Framework_TestCase
{
    public function testDisplay()
    {
        $this->addService('Token', $this->getMockForService(Token::class, 'Token', ['make']));
        $oJsonView = new \Core\Library\View\JsonView();
        $oJsonView->assign('test', 'test');
        $oJsonView->assign('test2', 'test');
        $oJsonView->assign('test3', 'test');

        $sExpected = json_encode([
            'meta' => ['token'=>null],
            'test' => 'test',
            'test2' => 'test',
            'test3' => 'test',
        ]);

        $this->assertEquals($sExpected, $oJsonView->render());
    }
}