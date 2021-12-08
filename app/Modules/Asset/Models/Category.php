<?php

namespace App\Modules\Asset\Models;

use App\Libraries\CommonFunction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'categories';

    public function subCategory()
    {
        return $this->hasMany(SubCategory::class, 'cat_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            $auth_user_id = CommonFunction::getUserId();
            $post->created_by = $auth_user_id;
            $post->updated_by = $auth_user_id;
        });

        static::updating(function ($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

    public static function dataList()
    {
        return Category::with('subCategory')
            ->orderBy('categories.id', 'desc')
            ->get();
    }
}
