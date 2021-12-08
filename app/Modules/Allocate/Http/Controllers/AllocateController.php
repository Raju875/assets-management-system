<?php

namespace App\Modules\Allocate\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Mail\AssignAsset;
use App\Models\User;
use App\Modules\Asset\Models\AssetList;
use App\Modules\Department\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class AllocateController extends Controller
{
    protected $module_name;

    public function __construct()
    {
        $this->module_name = 'Allocate';
    }

    public function list()
    {
        if (!ACL::getAccsessRight($this->module_name,'R')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [Aol.C-0005]']);
        }

        $page_title = $this->module_name;
        $departments = Department::where('status', 1)->pluck('name', 'id')->toArray();
        return view("Allocate::list", compact('page_title', 'departments'));
    }

    public function getList(Request $request)
    {
        try {
            $data = [
                'key' => $request->_key ? $request->_key : 'remaining',
                'dept_id' => $request->_dept_id ? $request->_dept_id : 'all'
            ];

            $list = AssetList::dataList($data);
            return DataTables::of($list)
                ->editColumn('checkbox', function ($list) { // only for new assign asset
                    return '<input type="checkbox" name="asset_ids[]" value="' . $list->id . '"/>';
                })
                ->editColumn('asset', function ($list) {
                    $asset = $list['asset'] ? $list['asset']['name'] : '-';
                    return wordwrap($asset, 20, "<br>\n") . '<br>' . '(' . $list->tracking_code . ')';
                })
                ->editColumn('cat_name', function ($list) {
                    return $list['category'] ? $list['category']['name'] : '-';
                })
                ->editColumn('sub_cat_name', function ($list) {
                    return $list['subCategory'] ? $list['subCategory']['name'] : '-';
                })
                ->editColumn('assign_to', function ($list) {
                    return $list['assignToUser'] ? $list['assignToUser']['name'] : '-';
                })
                ->editColumn('assign_by', function ($list) {
                    if ($list['updatedByUser']) {
                        return $list['updatedByUser']['name']
                            . '<br>'
                            . Carbon::createFromTimestamp(strtotime($list->updated_at))->diffForHumans();
                    }
                    return '-';
                })
                ->addColumn('action', function ($list) {
                    $html = '';

                    $html .= '<a href="' . route('asset-edit', ['id' => $list->asset_id]) .
                        '" class="btn btn-primary btn-xs" target="_blank"> <i class="fas fa-folder-open"></i> Open </a> ';

                    return $html;
                })
                ->addIndexColumn()
                ->rawColumns(['checkbox', 'asset', 'assign_by', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[Aol.C-1001]');
            return Redirect::back();
        }
    }

    //------ Assign Asset start
    public function assignAsset()
    {
        if (!ACL::getAccsessRight($this->module_name,'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [Aol.C-0010']);
        }

        try {
            $page_title = $this->module_name;
            $departments = ['' => 'Choose department'] + Department::where('status', 1)->pluck('name', 'id')->toArray();
            return view("Allocate::assign", compact('page_title', 'departments'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[Aol.C-1005]');
            return Redirect::back();
        }
    }

    public function getUserByDepartment(Request $request)
    {
        if (!ACL::getAccsessRight($this->module_name, 'R')) {
            return CommonFunction::dataResponse(false, '',
                'You have no access right! Please contact admin for more information. [Aol.C-0015]',
                201);
        }

        try {
            $users = User::where('dept_id', $request->dept_id)->orderBy('id', 'desc')->pluck('name', 'id')->toArray();

            if (!$users) {
                CommonFunction::dataResponse(false, '', 'Sorry! User not found in this type.', 201);
            }
            return CommonFunction::dataResponse(true, $users, 'User load successfully', 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'data' => '',
                'message' => CommonFunction::showErrorPublic($e->getMessage()) . '[Aol.C-1010]',
                'status' => 401
            ]);
        }
    }

    public function assignAssetStore(Request $request)
    {
        if (!ACL::getAccsessRight($this->module_name,'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [Aol.C-0020']);
        }

        try {
            parse_str($request->form_data, $data);

            if (count($data['asset_ids']) < 1) {
                return CommonFunction::dataResponse(false, '', 'Please select at least one item!', 201);
            }

            // duplicate entry check
            $assetInfo = AssetList::wherein('id', $data['asset_ids'])
                ->where('is_assign', 0) // 1=assign, 0=not assign
                ->whereNull('assign_user_id')
                ->whereNull('assign_dept_id')
                ->get();

            if (count($assetInfo) != count($data['asset_ids'])) {
                return CommonFunction::dataResponse(false, '',
                    'You have selected assets that have already assigned to another employee. Please check & try again.',
                    201);
            }

            // input field validation
            $rules = [
                'dept_id' => 'required',
                'user_id' => 'required'
            ];
            $validation = Validator::make($data, $rules);

            if ($validation->fails()) {
                return CommonFunction::dataResponse(false, '', $validation->errors(), 201);
            }

            DB::beginTransaction();
            foreach ($assetInfo as $asset) {
                $asset->is_assign = 1;
                $asset->assign_user_id = $data['user_id'];
                $asset->assign_dept_id = $data['dept_id'];
                $asset->save();
            }
            DB::commit();

            // send mail to user
            $user = User::where('id', $data['user_id'])->first(['name', 'email']);
            $content = [
                'title' => 'Assign asset',
                'body' => 'Dear ' . $user->name . ','
                    . '<br><br>' .
                    count($data['asset_ids']) . ' assets have been assigned to you.'
                    . '<br><br>' .
                    'Please login & check the asset list.'
                    . '<br><br>' .
                    'Contact admin if have more queries or observation.'
                    . '<br><br>' .
                    '<strong>Thanks & Regards,<strong>'
                    . '<br>Admin <br>Prolific Analytics'
            ];

            Mail::to($user->email)->send(new AssignAsset($content));

            // send mail to admin
            $admin = User::where('role_id', 1)->first(['name', 'email']); // 1(role_id)=Admin

            $content = [
                'title' => 'Assign asset',
                'body' => 'Dear ' . $admin->name . ','
                    . '<br><br>' .
                    count($data['asset_ids']) . ' assets have been assigned to ' . $user->name . '.'
                    . '<br><br>' .
                    'Please login & check the asset list.'
                    . '<br><br>' .
                    '<strong>Thanks & Regards,<strong>'
                    . '<br>Admin <br>' . config('app.name')
            ];

            Mail::to($admin->email)->send(new AssignAsset($content));

            return CommonFunction::dataResponse(true, '', 'Successfully assigned ' . count($data['asset_ids']) . ' items', 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => true,
                'data' => '',
                'message' => CommonFunction::showErrorPublic($e->getMessage()) . '[Aol.C-1010]',
                'status' => 401
            ]);
        }
    }
}
