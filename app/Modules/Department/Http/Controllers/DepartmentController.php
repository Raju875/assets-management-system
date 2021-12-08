<?php

namespace App\Modules\Department\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Modules\Department\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class DepartmentController extends Controller
{

    protected $module_name;

    public function __construct()
    {
        $this->module_name = 'Department';
    }

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        if (!ACL::getAccsessRight($this->module_name, 'R')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [DC-0005]']);
        }
        $page_title = $this->module_name;
        return view("Department::list", compact('page_title'));
    }

    public function getList()
    {
        try {
            $list = Department::dataList();
            return DataTables::of($list)
                ->editColumn('status', function ($list) {
                    return CommonFunction::getStatus($list->status);
                })
                ->addColumn('action', function ($list) {
                    $html = '';
                    $html .= '<a href="' . route('department-edit', ['id' => $list->id]) .
                        '" class="btn btn-primary btn-xs"> <i class="fa fa-folder-open"></i> Open </a> ';
                    return $html;
                })
                ->addIndexColumn()
                ->rawColumns(['status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[DC-1001]');
            return Redirect::back();
        }
    }

    public function add()
    {
        if (!ACL::getAccsessRight($this->module_name, 'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [DC-0010]']);
        }

        try {
            $page_title = $this->module_name;
            return view("Department::add", compact('page_title'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[DC-1005]');
            return Redirect::back();
        }
    }

    public function store(Request $request)
    {
        $department_id = (isset($request->department_id) ? $request->department_id : '');
        if (!ACL::getAccsessRight($this->module_name,$department_id ? 'UP' : 'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [DC-0015]']);
        }

        /* validation start */
        $rules = [];
        $message = [];

        // rules
        $rules['name'] = 'required|unique:departments,name' . ($department_id ? ",$department_id" : '');
        $rules['status'] = 'required';

        // custom message
        $message['name.required'] = 'Department name is required.';
        $message['name.unique'] = $request->name . ' has already been taken. Try unique name.';
        $message['status.required'] = 'Status is required.';

        $this->validate($request, $rules, $message);
        /* validation end */

        try {
            DB::beginTransaction();

            $departmentData = Department::findOrNew($department_id);
            $departmentData->name = $request->name;
            $departmentData->status = $request->status;
            $departmentData->save();

            DB::commit();

            Session::flash('success', 'Data is stored successfully!');
            return redirect()->route('department-list');

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[DC-1010]');
            return Redirect::back()->withInput();
        }
    }

    public function edit($id)
    {
        if (!ACL::getAccsessRight($this->module_name,'E')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [DC-0020]']);
        }
        $page_title = $this->module_name;
        try {
            $department_by_id = Department::find($id);
            return view('Department::edit', compact('page_title', 'department_by_id'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[DC-1015]');
            return Redirect::back();
        }
    }
}
