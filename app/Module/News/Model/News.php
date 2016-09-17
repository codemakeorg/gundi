<?php
namespace Module\News\Model;

use Core\Library\Database\Model;
use Core\Library\Validator\ValidatorTrait;

class News extends Model
{
    use ValidatorTrait;

    const ANONS_STR_LENGTH = 50;

    protected $table = 'news';

    protected $fillable = ['title', 'text', 'published'];

    /**
     * validate rules
     * @var array
     */
    protected $rules = [
        'title' => 'required|min:3|max:255',
        'text' => 'required|min:3|max:5000',
        'published' => 'in:0,1',
    ];

    /**
     * Anons
     * @return string
     */
    public function getAnonsAttribute()
    {
        $sText  = isset($this->attributes['text']) ? $this->attributes['text'] : '';
        return strip_tags(substr($sText, 0, self::ANONS_STR_LENGTH)) . '...';
    }


    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
}