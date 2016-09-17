<?php
namespace unit\core\library\router;

use Core\Contract\Request\IRequest;
use Core\Library\Router\Router;

class RouterTest extends \Gundi_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $oRequest;
    /**
     * @var Router
     */
    private $oRouter;

    public function setUp()
    {
        parent::setUp();
        $this->oRequest = $this->getMockForAbstractClass(IRequest::class, [], 'Request',
            false, false, true, ['getExt', 'getUri', 'getHttpMethod']);
        $this->oRouter = new Router($this->oRequest);
        $this->oRouter->error('testError@test');
    }


    public function testChainStyle()
    {
        $this->assertInstanceOf(Router::class, $this->oRouter->setBasePath('/'));
        $this->assertInstanceOf(Router::class, $this->oRouter->add('/test/', 'GET', 'testAction'));
        $this->assertInstanceOf(Router::class, $this->oRouter->get('/test/', 'testAction'));
        $this->assertInstanceOf(Router::class, $this->oRouter->post('/test/', 'testAction'));
        $this->assertInstanceOf(Router::class, $this->oRouter->put('/test/', 'testAction'));
        $this->assertInstanceOf(Router::class, $this->oRouter->delete('/test/', 'testAction'));
        $this->assertInstanceOf(Router::class, $this->oRouter->match('/test/', 'testAction', ['get', 'post']));
        $this->assertInstanceOf(Router::class, $this->oRouter->any('/test/', 'testAction'));
        $this->assertInstanceOf(Router::class, $this->oRouter->error(function () {
        }));
        $this->assertInstanceOf(Router::class, $this->oRouter->setRequest($this->oRequest));
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf(IRequest::class, $this->oRouter->getRequest());
    }

    public function testGetRouteExt()
    {
        $this->oRequest->expects($this->once())
            ->method('getExt')
            ->will($this->returnValue('json'));

        $this->assertEquals('json', $this->oRouter->getRouteExt());
    }

    /**
     * @dataProvider routeProvider
     */
    public function testGetHttpMethodRoute($sUri, $sRoute, $mHandler, $aHandlerData)
    {
        $this->oRequest->expects($this->once())->method('getUri')->will($this->returnValue($sUri));
        $this->oRequest->expects($this->once())->method('getHttpMethod')->will($this->returnValue('GET'));

        $this->oRouter->get($sRoute, $mHandler);
        $this->assertEquals($aHandlerData, $this->oRouter->getHandlerData());

    }

    /**
     * @dataProvider routeProvider
     */
    public function testPostHttpMethodRoute($sUri, $sRoute, $mHandler, $aHandlerData)
    {
        $this->oRequest->expects($this->once())->method('getUri')->will($this->returnValue($sUri));
        $this->oRequest->expects($this->once())->method('getHttpMethod')->will($this->returnValue('POST'));

        $this->oRouter->post($sRoute, $mHandler);
        $this->assertEquals($aHandlerData, $this->oRouter->getHandlerData());
    }

    /**
     * @dataProvider routeProvider
     */
    public function testPutHttpMethodRoute($sUri, $sRoute, $mHandler, $aHandlerData)
    {
        $this->oRequest->expects($this->once())->method('getUri')->will($this->returnValue($sUri));
        $this->oRequest->expects($this->once())->method('getHttpMethod')->will($this->returnValue('PUT'));

        $this->oRouter->put($sRoute, $mHandler);
        $this->assertEquals($aHandlerData, $this->oRouter->getHandlerData());
    }

    /**
     * @dataProvider routeProvider
     */
    public function testDeleteHttpMethodRoute($sUri, $sRoute, $mHandler, $aHandlerData)
    {
        $this->oRequest->expects($this->once())->method('getUri')->will($this->returnValue($sUri));
        $this->oRequest->expects($this->once())->method('getHttpMethod')->will($this->returnValue('DELETE'));

        $this->oRouter->delete($sRoute, $mHandler);
        $this->assertEquals($aHandlerData, $this->oRouter->getHandlerData());
    }

    public function routeProvider()
    {
        return [
            ['/test', '/test', 'testCtrl1@test', ['Controller' => 'testCtrl1', 'method' => 'test', 'vars' => []]],
            ['/test/uri', '/test/uri', 'testCtrl2@test2', ['Controller' => 'testCtrl2', 'method' => 'test2', 'vars' => []]],
            ['/func', 'func', function () {
            }, ['function' => function () {
            }, 'vars' => []]],
            ['/user/1', 'user/(:num)', function () {
            }, ['function' => function () {
            }, 'vars' => [1]]],
            ['/user/1/2', 'user/(:num)/(:num)', function () {
            }, ['function' => function () {
            }, 'vars' => [1, 2]]],
            ['/user/any_string', 'user/(:any)', function () {
            }, ['function' => function () {
            }, 'vars' => ['any_string']]],
            ['/api/any_string/1', '/api/(:all)', function () {
            }, ['function' => function () {
            }, 'vars' => ['any_string/1']]],
        ];
    }

    public function testErrorHandler()
    {
        $this->oRequest->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('/test'));

        $this->oRequest->expects($this->once())
            ->method('getHttpMethod')
            ->will($this->returnValue('GET'));

        $this->oRouter->get('', 'TestController@testAction')
            ->error('testError@test');

        $aActual = $this->oRouter->getHandlerData();

        $this->assertEquals([
            'Controller' => 'testError',
            'method' => 'test',
            'vars' => [],
        ], $aActual);

    }

    /**
     * @dataProvider resourceProvider
     */
    public function testResource($sRealUri, $sHttpMethod, $sRoute, $sClass, $aExclude, $aExpected)
    {
        $this->oRequest->expects($this->once())->method('getUri')->will($this->returnValue($sRealUri));
        $this->oRequest->expects($this->once())->method('getHttpMethod')->will($this->returnValue($sHttpMethod));

        $this->oRouter->error('error@notFound');
        $this->oRouter->resource($sRoute, $sClass, $aExclude, true);

        $this->assertEquals($aExpected, $this->oRouter->getHandlerData());
    }

    public function resourceProvider()
    {
        return [
            ['/users', 'GET', '/users', 'user', [], ['Controller' => 'user', 'method' => 'index', 'vars' => [],]],
            ['/users/qwe1', 'GET', '/users', 'user', [], ['Controller' => 'user', 'method' => 'show', 'vars' => ['qwe1'],]],
            ['/users/new', 'GET', 'users', 'user', [], ['Controller' => 'user', 'method' => 'add', 'vars' => [],]],
            ['/users/1/edit', 'GET', '/users', 'user', [], ['Controller' => 'user', 'method' => 'edit', 'vars' => [1],]],
            ['/users/1', 'PUT', '/users', 'user', [], ['Controller' => 'user', 'method' => 'update', 'vars' => [1],]],
            ['/users/1', 'DELETE', '/users', 'user', [], ['Controller' => 'user', 'method' => 'delete', 'vars' => [1],]],
            ['/users', 'POST', '/users', 'user', [], ['Controller' => 'user', 'method' => 'create', 'vars' => [],]],

            //with exclude
            ['/users', 'GET', '/users', 'user', ['edit', 'update'], ['Controller' => 'user', 'method' => 'index', 'vars' => [],]],
            ['/users/qwerty', 'GET', '/users', 'user', ['edit', 'update'], ['Controller' => 'user', 'method' => 'show', 'vars' => ['qwerty'],]],
            ['/user/new', 'GET', 'user', 'user', ['edit', 'update'], ['Controller' => 'user', 'method' => 'add', 'vars' => [],]],
            ['/users/qwerty/edit', 'GET', '/users', 'user', ['edit', 'update'], ['Controller' => 'error', 'method' => 'notFound', 'vars' => [],]],
            ['/users/qwerty', 'PUT', '/users', 'user', ['edit', 'update'], ['Controller' => 'error', 'method' => 'notFound', 'vars' => [],]],
            ['/users/qwerty', 'DELETE', '/users', 'user', ['edit', 'update'], ['Controller' => 'user', 'method' => 'delete', 'vars' => ['qwerty'],]],
            ['/users', 'POST', '/users', 'user', ['edit', 'update'], ['Controller' => 'user', 'method' => 'create', 'vars' => [],]],
        ];
    }

    /**
     * @dataProvider beforeTestProvider
     */
    public function testBefore($sRealUri, $sMethod, $aResourse, $aBeforeData, $sExpected)
    {
        $this->oRequest->expects($this->once())->method('getUri')->will($this->returnValue($sRealUri));
        $this->oRequest->expects($this->once())->method('getHttpMethod')->will($this->returnValue($sMethod));

        $this->oRouter->resource($aResourse[0], $aResourse[1]);
        $this->oRouter->before($aBeforeData['uri'], $aBeforeData['func']);
        $this->oRouter->getHandlerData();
        $this->expectOutputString($sExpected);
    }

    public function beforeTestProvider()
    {
        return [
            ['/user', 'GET', ['user', 'user'], ['uri' => 'user', 'func' => function () {
                echo 'test';
            }], 'test'],
            ['/user/1', 'GET', ['user', 'user'], ['uri' => 'user/(:num)', 'func' => function ($id) {
                echo 'user_' . $id;
            }], 'user_1'],
            ['/user/new', 'GET', ['user', 'user'], ['uri' => 'user/new', 'func' => function () {
                echo 'new user';
            }], 'new user'],
            ['/user/1/edit', 'GET', ['user', 'user'], ['uri' => 'user/(:num)/edit', 'func' => function ($id) {
                echo 'edit_form_' . $id;
            }], 'edit_form_1'],
            ['/user/1/edit', 'PUT', ['user', 'user'], ['uri' => 'user/(:num)/edit', 'func' => function () {
                echo 'test';
            }], 'test'],
            ['/user/1', 'DELETE', ['user', 'user'], ['uri' => 'user/(:num)', 'func' => function () {
                echo 'del';
            }], 'del'],
            ['/user', 'POST', ['user', 'user'], ['uri' => 'user', 'func' => function () {
                echo '123';
            }], '123'],
        ];
    }
    /**
     * @dataProvider curRouteAndVarsProvider
     */
    public function testGetCurrentRouteAndVars($sRealUri, $sUri, $sCallback, $aVars)
    {
        $this->oRequest->expects($this->once())->method('getUri')->will($this->returnValue($sRealUri));
        $this->oRequest->expects($this->once())->method('getHttpMethod')->will($this->returnValue('GET'));
        $this->oRouter->get($sUri, $sCallback);
        $this->oRouter->getHandlerData();
        $this->assertEquals($sUri, $this->oRouter->getCurrentRoute());
        $this->assertEquals($aVars, $this->oRouter->getCurrentVars());
    }

    public function curRouteAndVarsProvider()
    {
        return [
            ['/test', '/test', 'test/Controller@index', []],
            ['/test/1', '/test/(:num)', 'test/Controller@index', [1]],
            ['/test/any', '/test/(:any)', 'test/Controller@index', ['any']],
        ];
    }
}