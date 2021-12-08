<?php

namespace App\Modules\User\Models;

use App\Libraries\CommonFunction;
use App\Modules\Asset\Models\AssetList;
use App\Modules\Department\Models\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $guarded = ['id'];

    public function assignAsset()
    {
        return $this->hasMany(AssetList::class, 'assign_user_id', 'id');
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
        $query = User::with(['assignAsset'])
            ->leftJoin('user_role', 'user_role.id', '=', 'users.role_id')
            ->leftJoin('departments', 'departments.id', '=', 'users.dept_id');

        if (Auth::user()->role_id == 1) {
            $query->orderBy('users.id', 'desc');
        } else {
            $query->where('users.id', Auth::id());
        }
        return $query->get([
            'user_role.name as user_role',
            'departments.name as department',
            'users.id',
            'users.name',
            'users.email',
            'users.status'
        ]);
    }
}
