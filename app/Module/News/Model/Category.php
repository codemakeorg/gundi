<?php
namespace Module\News\Model;

use Core\Library\Database\Model;
use Core\Library\Validator\ValidatorTrait;
use Module\News\Model\News;

class Category extends Model
{
    protected $table = 'categories';

    use ValidatorTrait;

    protected $fillable = ['id', 'name', 'description'];

    /**
     * validate rules
     * @var array
     */
    protected $rules = [
        'name' => 'required|min:3|max:255',
    ];

    /**
     * Validate error messages
     * @var array
     */
    protected $validationMessages = [
        'name.required' => '"Name" field is required',
    ];

    /**
     * News object relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news()
    {
        return $this->hasMany(News::class, 'category_id', 'id');
    }

    /**
     * Register cascading delete news action
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(
            //delete news of category
            function ($category) {
                $category->news()->delete();
            }
        );
    }
}