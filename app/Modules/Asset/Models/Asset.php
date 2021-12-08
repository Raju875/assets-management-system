<?php

namespace App\Modules\Asset\Models;

use App\Libraries\CommonFunction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'assets';

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'cat_id');
    }

    public function subCategory()
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_cat_id');
    }

    public function assetList()
    {
        return $this->hasMany(AssetList::class, 'asset_id', 'id');
    }

    public function assignTo()
    {
        return $this->hasOne(User::class, 'id', 'assign_user_id');
    }

    public function updatedByUser()
    {
        return $this->hasOne(User::class, 'id', 'updated_by');
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
        return Asset::with(['category', 'subCategory', 'assetList'])
            ->orderBy('assets.id', 'desc')
            ->get();
    }
}
