<?php

namespace Module\News\Component\Controller;

use Core\Contract\Request\IRequest;
use Core\Contract\Resource\IAddable;
use Core\Contract\Resource\IDeleteable;
use Core\Contract\Resource\IEditable;
use Core\Contract\Resource\IShowable;
use Core\Library\Component\Controller;
use Core\Library\Error\Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Module\News\Library\Search;
use Module\News\Model\News as NewsModel;
use Module\News\Model\Category as CategoryModel;
use Watson\Validating\ValidationException;

class News extends Controller implements IAddable, IEditable, IShowable, IDeleteable
{
    const PER_PAGE = 10;
    /**
     * @var NewsModel
     */
    private $_oModel;
    /**
     * @var CategoryModel
     */
    private $_oCategoryModel;
    /**
     * @var IRequest
     */
    private $_oRequest;
    /**
     * @var Error
     */
    private $_oError;
    /**
     * @var Search
     */
    private $_oSearch;

    public function __construct(
        NewsModel $oNewsModel,
        CategoryModel $oCategoryModel,
        IRequest $oRequest,
        Search $oSearch,
        Error $oError
    )
    {
        $this->_oModel = $oNewsModel;
        $this->_oCategoryModel = $oCategoryModel;
        $this->_oRequest = $oRequest;
        $this->_oError = $oError;
        $this->_oSearch = $oSearch;
    }

    /**
     * Get list of categories
     * @return void
     */
    public function index()
    {
        $iPage = (int)$this->_oRequest->get('page', 1);
        $iPerPage = (int)$this->_oRequest->get('per_page', self::PER_PAGE);
        $aCriteria = $this->_oSearch->normalizeCriteria((array)$this->_oRequest->get('filter', []));

        $aNews = $this->_oModel
            ->with('category')
            ->where($aCriteria)
            ->orderBy($this->_oModel->getKeyName(), 'DESC')
            ->paginate($iPerPage, ['*'], 'news', $iPage);
        $aCategories = $this->_oCategoryModel->all();

        $this->oView->assign('news', $aNews);
        $this->oView->assign('categories', $aCategories);
    }

    /**
     * Show add form
     * @return void
     */
    public function add()
    {
        $this->oView->assign('categories', $this->_oCategoryModel->all());
    }

    /**
     * Save to DB
     * @return void
     */
    public function create()
    {
        try {

            $aNews = $this->_oRequest->get('news', []);
            $iCategoryId = isset($aNews['category_id']) ? $aNews['category_id'] : 0;

            /**
             * @var $oCategory CategoryModel
             */
            $oCategory = $this->_oCategoryModel->findOrFail($iCategoryId);
            $this->_oModel->fill($aNews);
            $this->_oModel->saveOrFail();
            $oCategory->news()->save($this->_oModel);//save relation

            $this->oView->assign([
                'message' => 'News successfully added',
                'news' => $this->_oModel
            ]);

        } catch (ValidationException $e) {

            //if is not valid data
            $this->_oError->display('Provide all fields', 406, $this->_oRequest->getExt(),
                [
                    'errors' => $e->getErrors(),
                    'news' => $this->_oModel,
                ]);

        } catch (ModelNotFoundException $e) {

            //if is not selected category
            $this->_oError->display('Select category', 406, $this->_oRequest->getExt(),
                [
                    'errors' => ['category' => ['Select category']],
                    'news' => $this->_oModel,
                ]);

        }
    }

    /**
     * Delete item from database
     * @param string|int $mID
     * @return void
     */
    public function delete($mID)
    {
        try {

            $oModel = $this->_oModel->findOrFail($mID);
            $oModel->delete();
            $this->oView->assign('message', 'Successfully deleted');

        } catch (ModelNotFoundException $e) {

            $this->_oError->display('News not found with id:' . $mID, 404, $this->_oRequest->getExt());

        }
    }

    /**
     * Show edit form
     * @param string|int $mId
     */
    public function edit($mId)
    {
        $this->newsData($mId);
    }

    /**
     * Update category
     * @param string|int $mId
     */
    public function update($mId)
    {
        $aNews = (array)$this->_oRequest['news'];

        try {

            $oNews = $this->_oModel->findOrFail($mId);
            $oNews->fill($aNews);

            if (isset($aNews['category_id'])) {
                $oNews->category_id = $aNews['category_id'];
            }

            $oNews->saveOrFail();

            $this->oView->assign('news', $oNews);
            $this->oView->assign('message', 'Success updated');

        } catch (ValidationException $e) {

            $this->_oError->display(
                'Provide all fields',
                406,
                $this->_oRequest->getExt(),
                ['news' => $aNews, 'errors' => $e->getErrors()]
            );

        } catch (ModelNotFoundException $e) {

            $this->_oError->display('News not found with id:' . $mId, 404, $this->_oRequest->getExt(), ['news' => $aNews]);

        }
    }

    /**
     * Show resource
     * @param $mId
     * @return void
     */
    public function show($mId)
    {
        $this->newsData($mId);
    }

    /**
     * Hide news action
     * @param $mId
     */
    public function hide($mId)
    {
        try {

            $oNews = $this->_oModel->findOrFail($mId);
            $oNews->published = 0;
            $oNews->save();

            $this->oView->assign('news', $oNews);

        } catch (ModelNotFoundException $e) {

            $this->_oError->display('News not found with id:' . $mId, 404, $this->_oRequest->getExt());

        }
    }

    /**
     * Publish action
     * @param $mId
     */
    public function publish($mId)
    {
        //todo::tdd test
        try {
            $oNews = $this->_oModel->findOrFail($mId);
            $oNews->published = 1;
            $oNews->save();
            $this->oView->assign('news', $oNews);
        } catch (ModelNotFoundException $e) {
            $this->_oError->display('News not found with id:' . $mId, 404, $this->_oRequest->getExt());
        }
    }

    private function newsData($mId)
    {
        try {

            $this->oView->assign('news', $this->_oModel->with('category')->findOrFail($mId));
            $this->oView->assign('categories', $this->_oCategoryModel->all());

        } catch (ModelNotFoundException $oException) {

            $this->_oError->display('News not found with id:' . $mId, 404, $this->_oRequest->getExt());

        }

    }
}
