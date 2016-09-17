<?php
namespace tests\unit\module\news\controller;

use Core\Contract\Request\IRequest;
use Core\Library\Error\Error;
use Core\Library\View\JsonView;
use Module\News\Component\Controller\Category;
use Module\News\Component\Controller\News;
use Module\News\Model\Category as CategoryModel;
use Module\News\Model\News as NewsModel;
use Tests\unit\Stubs\StubError;
use Tests\unit\Stubs\StubRequest;

class NewsTest extends \Gundi_Framework_TestCase
{

    /**
     * @var NewsModel
     */
    private $oModel;

    /**
     * @var CategoryModel
     */
    private $oCategoryModel;


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

        $this->oModel = new NewsModel();
        $this->oCategoryModel = new CategoryModel();
        $this->oModel->truncate();
        $this->oCategoryModel->truncate();
    }


    public function testIndex()
    {
        $oCategory = $this->oCategoryModel->create(['name' => 'testCategory']);

        $aNewsFixture =
            [
                'news1' => ['title' => 'news1', 'text' => 'text1', 'published' => 0],
                'news2' => ['title' => 'news2', 'text' => 'text2', 'published' => 1],
                'news3' => ['title' => 'news3', 'text' => 'text2', 'published' => 0],
            ];

        foreach ($aNewsFixture as $sNews => $aNews) {
            $oNews = $this->oModel->create($aNews);
            $oCategory->news()->save($oNews);
        }

        $aNewsFixture = array_reverse($aNewsFixture);

        //test paginate
        $i = 1;
        foreach ($aNewsFixture as $sNewsTitle => $aNews) {
            $this->make(IRequest::class)->exchangeArray([
                'filter' => [],
                'page' => $i++,
                'per_page' => 1,
            ]);
            /**
             * @var $oController News
             */
            $oController = $this->makeController();
            $oController->index();
            $aActualCategory = array_shift($this->oView->getVar('news')->toArray()['data']);
            $this->assertEquals($sNewsTitle, $aActualCategory['title']);
        }

        $this->make(IRequest::class)->exchangeArray([
            'filter' => [],
            'page' => 0,
            'per_page' => 1,
        ]);
        $oController = $this->makeController();
        $oController->index();
        $oActualNews = array_shift($this->oView->getVar('news')->toArray()['data']);
        $this->assertEquals('news3', $oActualNews['title']);
//        var_dump($this->oView->getVar('categories')[0]['name']); die;
        $this->assertEquals('testCategory', $this->oView->getVar('categories')[0]['name']);

        //test filter
        $this->make(IRequest::class)->exchangeArray([
            'filter' => ['published' => 1],
            'page' => 0,
            'per_page' => 1,
        ]);
        $oController = $this->makeController();
        $oController->index();
        $oActualNews = array_shift($this->oView->getVar('news')->toArray()['data']);
        $this->assertEquals('news2', $oActualNews['title']);
    }

    public function testCreate()
    {
        $oCategory = $this->oCategoryModel->create(['name' => 'testCategory']);
        $this->make(IRequest::class)->exchangeArray([
            'news' => ['title' => 'test', 'text' => 'text test', 'published' => 1, 'category_id' => $oCategory->getKey()],
        ]);

        $oController = $this->makeController();
        $oController->create();
        $oView = $oController->getView();
        $this->assertEquals('News successfully added', $oView->getVar('message'));

        //test validate
        $this->make(IRequest::class)->exchangeArray([
            'news' => ['category_id' => $oCategory->getKey()],
        ]);
        $oController = $this->makeController();
        $oController->create();
        $this->expectOutputString('Provide all fields');
        $aActualErrors = $this->oView->getVar('errors')->getMessages();
        $this->assertArrayHasKey('title', $aActualErrors);
        $this->assertEquals('validation.required', $aActualErrors['title'][0]);
    }

    public function testDelete()
    {
        $oController = $this->makeController();
        $oController->delete('qwerty');
        $this->expectOutputString('News not found with id:qwerty');
        $oNews = $this->oModel->create(['title' => 'testDelete', 'text' => 'test', 'published' => 0]);
        $oController->delete($oNews->getKey());
        $this->assertEquals('Successfully deleted', $oController->getView()->getVar('message'));
    }

    public function testEdit()
    {
        $oNews = $this->oModel->create(['title' => 'testEdit', 'text' => 'text', 'published' => 1]);
        $oController = $this->makeController();
        $oController->edit($oNews->getKey());
        $this->assertEquals('testEdit', $oController->getView()->getVar('news')['title']);

        $oController->edit('qazx');
        $this->expectOutputString('News not found with id:qazx');
    }


    public function testUpdate()
    {
        //test success update
        $oCategory = $this->oCategoryModel->create(['name' => 'testCategory']);
        $this->make(IRequest::class)->exchangeArray([
            'news' => ['title' => 'testUpdate', 'text' => 'testUpdateDesc', 'published' => 0, 'category_id' => $oCategory->getKey()],
        ]);

        $oCat = $this->oModel->create(['title' => 'testUpdate']);
        $oController = $this->makeController();
        $oController->update($oCat->getKey());
//        var_dump(['text']); die;
        $oActualNews = $this->oView->getVar('news');
        $this->assertEquals('Success updated', $this->oView->getVar('message'));
        $this->assertEquals('testUpdateDesc', $oActualNews['text']);
        $this->assertEquals('testCategory', $oActualNews['category']['name']);

        //test not found
        $oController->update('qwerty');
        $this->expectOutputString('News not found with id:qwerty');
    }

    public function testShow()
    {
        $oNews = $this->oModel->create(['title' => 'testShow', 'text' => 'sdkjfkl', 'published' => 1]);
        $oController = $this->makeController();
        $oController->edit($oNews->getKey());
        $this->assertEquals('testShow', $oController->getView()->getVar('news')['title']);

        $oController->edit('qazx');
        $this->expectOutputString('News not found with id:qazx');
    }

    public function testHide()
    {
        $oNews = $this->oModel->create(['title' => 'testShow', 'text' => 'sdkjfkl', 'published' => 1]);
        $oController = $this->makeController();
        $oController->hide($oNews->getKey());
        $this->assertEquals('testShow', $oController->getView()->getVar('news')['title']);
        $this->assertEquals(0, $oController->getView()->getVar('news')['published']);

        $oController->edit('qazx');
        $this->expectOutputString('News not found with id:qazx');
    }

    public function testPublish()
    {
        $oNews = $this->oModel->create(['title' => 'testShow', 'text' => 'sdkjfkl', 'published' => 0]);
        $oController = $this->makeController();
        $oController->publish($oNews->getKey());
        $this->assertEquals('testShow', $oController->getView()->getVar('news')['title']);
        $this->assertEquals(1, $oController->getView()->getVar('news')['published']);

        $oController->edit('qazx');
        $this->expectOutputString('News not found with id:qazx');
    }

    /**
     * @return News
     */
    private function makeController()
    {
        $oController = $this->make(News::class);
        $oController->setView($this->oView);
        return $oController;
    }

}

