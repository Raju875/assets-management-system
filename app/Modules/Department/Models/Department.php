<?php

namespace App\Modules\Department\Models;

use App\Libraries\CommonFunction;
use App\Modules\Department\Events\DepartmentCreatingEvent;
use App\Modules\Department\Events\DepartmentUpdatingEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;


    protected $guarded = ['id'];

    protected $table = 'departments';

    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($post) {
    //         $auth_user_id = CommonFunction::getUserId();
    //         $post->created_by = $auth_user_id;
    //         $post->updated_by = $auth_user_id;
    //     });

    //     static::updating(function ($post) {
    //         $post->updated_by = CommonFunction::getUserId();
    //     });
    // }

    protected $dispatchesEvents = [
        'creating' => DepartmentCreatingEvent::class,
        'updating' => DepartmentUpdatingEvent::class,
    ];

    public static function dataList()
    {
        return Department::orderBy('id', 'desc')
            ->get([
                'id',
                'name',
                'status',
            ]);
    }
}
