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
use Module\News\Model\Category as CategoryModel;
use Watson\Validating\ValidationException;

class Category extends Controller implements IAddable, IEditable, IShowable, IDeleteable
{
    const CATEGORY_PER_PAGE = 10;
    /**
     * @var CategoryModel
     */
    private $_oModel;
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
        CategoryModel $oModelCategories,
        IRequest $oRequest,
        Search $oSearch,
        Error $oError
    )
    {
        $this->_oModel = $oModelCategories;
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
        $iPerPage = (int)$this->_oRequest->get('per_page', self::CATEGORY_PER_PAGE);
        $aCriteria =  $this->_oSearch->normalizeCriteria((array)$this->_oRequest->get('filter', []));

        $aCategories = $this->_oModel
            ->with(['news'])
            ->where($aCriteria)
            ->orderBy($this->_oModel->getKeyName(), 'DESC')
            ->paginate($iPerPage, ['*'], 'categories', $iPage);

        $this->oView->assign('categories', $aCategories);
    }

    /**
     * Show add form
     * @return void
     */
    public function add()
    {
    }

    /**
     * Save to DB
     * @return void
     */
    public function create()
    {
        try {
            $aCategory = $this->_oRequest['category'];
            $this->_oModel->fill($aCategory);
            $this->_oModel->saveOrFail();
            $this->oView->assign([
                'message' => 'Category successfully added',
                'category' => $this->_oModel
            ]);
        } catch (ValidationException $e) {
            //if is not valid data
            $this->_oError->display('Provide all fields', 406, $this->_oRequest->getExt(),
                [
                    'errors' => $e->getErrors(),
                    'category' => $this->_oModel,
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
            $this->oView->assign('message', $oModel->name . ' successfully deleted');
        } catch (ModelNotFoundException $e) {
            $this->_oError->display('Category not found with id:' . $mID, 404, $this->_oRequest->getExt());
        }
    }

    /**
     * Show edit form
     * @param string|int $mId
     */
    public function edit($mId)
    {
        $this->categoryData($mId);
    }

    /**
     * Update category
     * @param string|int $mId
     */
    public function update($mId)
    {
        $aCategory = (array)$this->_oRequest['category'];
        try {
            $oCategory = $this->_oModel->findOrFail($mId);
            $oCategory->fill($aCategory);
            $oCategory->saveOrFail();
            $this->oView->assign('category', $oCategory);
            $this->oView->assign('message', 'Success updated');
        } catch (ValidationException $e) {
            $this->_oError->display(
                'Provide all fields',
                406,
                $this->_oRequest->getExt(),
                ['category' => $aCategory, 'errors' => $e->getErrors()]
            );
        } catch (ModelNotFoundException $e) {
            $this->_oError->display('Category not found with id:' . $mId, 404, $this->_oRequest->getExt(), ['category' => $aCategory]);
        }
    }

    /**
     * Show resource
     * @param $mId
     * @return void
     */
    public function show($mId)
    {
        $this->categoryData($mId);
    }

    private function categoryData($mId)
    {
        try {
            $this->oView->assign('category', $this->_oModel->findOrFail($mId));
        } catch (ModelNotFoundException $oException) {
            $this->_oError->display('Category not found with id:' . $mId, 404, $this->_oRequest->getExt());
        }
    }
}
