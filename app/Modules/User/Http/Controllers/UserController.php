<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Modules\Asset\Models\Asset;
use App\Modules\Asset\Models\Category;
use App\Modules\Asset\Models\SubCategory;
use App\Modules\Department\Models\Department;
use App\Modules\User\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    protected $module_name;

    public function __construct()
    {
        $this->module_name = 'User';
    }

    public function list()
    {
        if (!ACL::getAccsessRight($this->module_name, 'R')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [UC-0005]']);
        }
        $page_title = $this->module_name;
        return view("User::list", compact('page_title'));
    }

    public function getList()
    {
        try {
            $list = User::dataList();
            return DataTables::of($list)
                ->editColumn('user_info', function ($list) {
                    return "Name: $list->name <br>
                    Email: $list->email <br>
                    Role: $list->user_role <br>
                    Department: $list->department";
                })
                ->editColumn('asset', function ($list) {
                    if ($list['assignAsset']) {
                        $html = '';
                        foreach ($list['assignAsset'] as $key => $asset) {
                            $key++;
                            $cat_name = Category::where('id', $asset['cat_id'])->first(['name'])->name;
                            $sub_cat_name = SubCategory::where('id', $asset['sub_cat_id'])->first(['name'])->name;
                            $asset_name = Asset::where('id', $asset['asset_id'])->first(['name'])->name;
                            $time = Carbon::createFromTimestamp(strtotime($asset->updated_at))->diffForHumans();

                            $html .= "$key. Name: $asset_name ($asset->tracking_code) <br>
                            Category: $cat_name <br>
                            Sub Category: $sub_cat_name <br>
                            Department: $list->department <br>
                            $time <br><br>";
                        }
                        return $html;
                    }
                })
                ->editColumn('status', function ($list) {
                    return CommonFunction::getStatus($list->status);
                })
                ->addColumn('action', function ($list) {
                    $html = '';
                    $html .= '<a href="' . route('user-edit', ['id' => $list->id]) .
                        '" class="btn btn-primary btn-xs"> <i class="fa fa-folder-open"></i> Open </a> ';
                    if (Auth::user()->role_id == 1) {
                        $html .= '<a href="' . route('allocate-assign-asset') .
                            '" class="btn btn-success btn-xs"> <i class="fa fa-plus-square"></i> Assign Asset</a> ';
                    }
                    return $html;
                })
                ->addIndexColumn()
                ->rawColumns(['user_info', 'asset', 'status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[UC-1001]');
            return Redirect::back();
        }
    }

    public function add()
    {
        if (!ACL::getAccsessRight($this->module_name, 'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [UC-0010]']);
        }

        try {
            $page_title = $this->module_name;
            $departments = ['' => '--Select department--'] + Department::where('status', 1)->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
            return view("User::add", compact('page_title', 'departments'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[UC-1005]');
            return Redirect::back();
        }
    }

    public function store(Request $request)
    {
        $user_id = (isset($request->user_id) ? $request->user_id : '');

        if (!ACL::getAccsessRight($this->module_name, $user_id ? 'UP' : 'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [UC-0015]']);
        }

        /* validation start */
        $rules = [];
        $message = [];

        // rules
        $rules['name'] = 'required|unique:users,name' . ($user_id ? ",$user_id" : '');
        $rules['email'] = 'required|unique:users,email' . ($user_id ? ",$user_id" : '');
        $rules['dept_id'] = (Auth::user()->role_id = 1) ? '' : 'required';
        $rules['status'] = 'required';

        // custom message
        $message['name.required'] = 'User name is required.';
        $message['name.unique'] = $request->name . ' has already been taken. Try unique name.';
        $message['email.required'] = 'Email is required.';
        $message['email.unique'] = $request->email . ' has already been taken. Try unique email.';
        $message['dept_id.required'] = 'Department is required.';
        $message['status.required'] = 'Status is required.';

        $this->validate($request, $rules, $message);
        /* validation end */

        try {
            DB::beginTransaction();

            $userData = User::findOrNew($user_id);
            $userData->name = $request->name;
            $userData->email = $request->email;
            $userData->role_id = $user_id ? $userData->role_id : 2; // 1=admin, 2=employee
            $userData->password = $user_id ? $userData->password : Hash::make('password');
            $userData->dept_id = $request->dept_id;
            $userData->status = $request->status;
            $userData->save();

            DB::commit();

            Session::flash('success', 'Data is stored successfully!');
            return redirect()->route('user-list');

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[UC-1010]');
            return Redirect::back()->withInput();
        }
    }

    public function edit($id)
    {
        if (!ACL::getAccsessRight($this->module_name, 'E')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [UC-0020]']);
        }

        $page_title = $this->module_name;
        try {
            $user_by_id = User::find($id);
            $query = Department::where('status', 1);

            if (Auth::user()->role_id == 1) {
                $query->orderBy('id', 'desc');
            } else {
                $query->where('id', Auth::user()->dept_id);
            }
            $departments = ['' => '--Select department--'] + $query->pluck('name', 'id')->toArray();

            return view('User::edit', compact('page_title', 'user_by_id', 'departments'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[UC-1015]');
            return Redirect::back();
        }
    }
}
