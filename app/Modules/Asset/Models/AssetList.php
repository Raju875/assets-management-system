<?php

namespace App\Modules\Asset\Models;

use App\Libraries\CommonFunction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetList extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'asset_list';

    public function category()
    {
        return $this->hasOne(Category::class, 'id', 'cat_id');
    }

    public function subCategory()
    {
        return $this->hasOne(SubCategory::class, 'id', 'sub_cat_id');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id', 'id');
    }

    public function assignToUser()
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

    public static function dataList($data)
    {
        $query = AssetList::with(['category' => function ($q) {
            $q->select('id', 'name');
        }])
            ->with(['subCategory' => function ($q) {
                $q->select('id', 'name');
            }])
            ->with(['asset' => function ($q) {
                $q->select('id', 'name');
            }])
            ->with(['assignToUser' => function ($q) {
                $q->select('id', 'name', 'dept_id');
            }]);

        // tab wise data fetch
        if ($data['key'] == 'remaining') { // remaining asset list
            $query->where('asset_list.is_assign', 0) //1=assign, 0=not assign
            ->whereNull('assign_user_id');

        } else {
            $query->with(['updatedByUser' => function ($q) {
                $q->select('id', 'name');
            }])
                ->where('is_assign', 1) //1=assign, 0=not assign
                ->whereNotNull('assign_user_id');

            if ($data['key'] == 'assigned') { // assigned asset list

            } elseif ($data['key'] == 'department_basis') {// department basis asset list
                if ($data['dept_id'] && $data['dept_id'] != 'all') {
                    $query->where('assign_dept_id', $data['dept_id']);
                }

            } elseif ($data['key'] == 'time_basis') {// allocated days wise asset list
                $query->where('updated_at', '>=', Carbon::now()->subDays(5));
            }
        }

        return $query->orderBy('id', 'desc')->get();
    }
}
