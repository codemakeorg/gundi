<?php
namespace tests\unit\module\news\controller;

use Core\Contract\Request\IRequest;
use Core\Library\Error\Error;
use Core\Library\View\JsonView;
use Module\News\Component\Controller\Category;
use Module\News\Model\Category as CategoryModel;
use Tests\unit\Stubs\StubError;
use Tests\unit\Stubs\StubRequest;

class CategoryTest extends \Gundi_Framework_TestCase
{

    /**
     * @var CategoryModel
     */
    private $oModel;


    /**
     * @var JsonView
     */
    private $oView;

    public function setUp()
    {
        parent::setUp();
        $this->oView = new JsonView();
        StubError::$oView = $this->oView;
        $this->addService(IRequest::class, new StubRequest());
        $this->addService(Error::class, new StubError());
        $this->oModel = new CategoryModel();
        $this->oModel->truncate();
    }


    public function testIndex()
    {
        $aCategoriesFixture =
            [
                'cat1' => ['name' => 'cat1'],
                'cat2' => ['name' => 'cat2'],
                'cat3' => ['name' => 'cat3'],
            ];

        foreach ($aCategoriesFixture as $sCatName => $aCategory) {
            $this->oModel->forceCreate($aCategory);
        }

        $aCategoriesFixture = array_reverse($aCategoriesFixture);

        //test paginate
        $i = 1;
        foreach ($aCategoriesFixture as $sCatName => $aCategory) {
            $this->make(IRequest::class)->exchangeArray([
                'filter' => [],
                'page' => $i++,
                'per_page' => 1,
            ]);
            /**
             * @var $oController Category
             */
            $oController = $this->makeController();
            $oController->index();
            $aActualCategory = array_shift($this->oView->getVar('categories')->toArray()['data']);
            $this->assertEquals($sCatName, $aActualCategory['name']);
        }

        $this->make(IRequest::class)->exchangeArray([
            'filter' => [],
            'page' => 0,
            'per_page' => 1,
        ]);
        $oController = $this->makeController();
        $oController->index();
        $oActualNews = array_shift($this->oView->getVar('categories')->toArray()['data']);
        $this->assertEquals('cat3', $oActualNews['name']);

        //test filter
        $this->make(IRequest::class)->exchangeArray([
            'filter' => ['name__equal' => 'cat1'],
            'page' => 0,
            'per_page' => 1,
        ]);
        $oController = $this->makeController();
        $oController->index();
        $oActualNews = array_shift($this->oView->getVar('categories')->toArray()['data']);
        $this->assertEquals('cat1', $oActualNews['name']);
    }

    public function testCreate()
    {
        $this->make(IRequest::class)->exchangeArray([
            'category' => ['name' => 'test', 'description' => 'desc test'],
        ]);

        $oController = $this->makeController();
        $oController->create();
        $oView = $oController->getView();
        $this->assertEquals('Category successfully added', $oView->getVar('message'));

        //test validate
        $this->make(IRequest::class)->exchangeArray([
            'category' => [],
        ]);
        $oController = $this->makeController();
        $oController->create();
        $this->expectOutputString('Provide all fields');
        $aActualErrors = $this->oView->getVar('errors')->getMessages();
        $this->assertArrayHasKey('name', $aActualErrors);
        $this->assertEquals('"Name" field is required', $aActualErrors['name'][0]);
    }

    public function testDelete()
    {
        $oController = $this->makeController();
        $oController->delete('qwerty');
        $this->expectOutputString('Category not found with id:qwerty');
        $oCat = $this->oModel->forceCreate(['name' => 'testDelete']);
        $oController->delete($oCat->getKey());
        $this->assertEquals($oCat->name . ' successfully deleted', $oController->getView()->getVar('message'));
    }

    public function testEdit()
    {
        $oCat = $this->oModel->forceCreate(['name' => 'testEdit']);
        $oController = $this->makeController();
        $oController->edit($oCat->getKey());
        $this->assertEquals('testEdit', $oController->getView()->getVar('category')['name']);

        $oController->edit('qazx');
        $this->expectOutputString('Category not found with id:qazx');
    }


    public function testUpdate()
    {
        //test success update
        $this->make(IRequest::class)->exchangeArray([
            'category' => ['name' => 'testUpdate', 'description' => 'testUpdateDesc'],
        ]);

        $oCat = $this->oModel->forceCreate(['name' => 'testUpdate']);
        $oController = $this->makeController();
        $oController->update($oCat->getKey());
        $this->assertEquals('Success updated', $this->oView->getVar('message'));
        $this->assertEquals('testUpdateDesc', $this->oView->getVar('category')['description']);

        //test not found
        $oController->update('qwerty');
        $this->expectOutputString('Category not found with id:qwerty');
    }

    public function testShow()
    {
        $oCat = $this->oModel->forceCreate(['name' => 'testShow']);
        $oController = $this->makeController();
        $oController->edit($oCat->getKey());
        $this->assertEquals('testShow', $oController->getView()->getVar('category')['name']);

        $oController->edit('qazx');
        $this->expectOutputString('Category not found with id:qazx');
    }

    /**
     * @return Category
     */
    private function makeController()
    {
        $oController = $this->make(Category::class);
        $oController->setView($this->oView);
        return $oController;
    }

}

