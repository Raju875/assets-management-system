<?php

namespace App\Modules\Asset\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Modules\Asset\Models\Asset;
use App\Modules\Asset\Models\AssetList;
use App\Modules\Asset\Models\Category;
use App\Modules\Asset\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class AssetController extends Controller
{
    protected $module_name;

    public function __construct()
    {
        $this->module_name = 'Asset';
    }

    public function list()
    {
        if (!ACL::getAccsessRight($this->module_name, 'R')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [AC-0005]']);
        }
        $page_title = $this->module_name;
        return view("Asset::asset.list", compact('page_title'));
    }

    public function getList()
    {
        try {
            $list = Asset::dataList();
            return DataTables::of($list)
                ->editColumn('cat_name', function ($list) {
                    return $list['category'] ? $list['category']['name'] : '--';
                })
                ->editColumn('sub_cat_name', function ($list) {
                    return $list['subCategory'] ? $list['subCategory']['name'] : '--';
                })
                ->editColumn('tracking_code', function ($list) {
                    $html = '';
                    if ($list['assetList']) {
                        foreach ($list['assetList'] as $assetList) {
                            $html .= '<ul><li> ' . $assetList["tracking_code"] . ' </li></ul> ';
                        }
                    }
                    return $html;
                })
                ->addColumn('action', function ($list) {
                    $html = '';
                    $html .= '<a href="' . route('asset-edit', ['id' => $list->id]) .
                        '" class="btn btn-primary btn-xs"> <i class="fa fa-folder-open"></i> Open </a> ';
                    return $html;
                })
                ->addIndexColumn()
                ->rawColumns(['tracking_code', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[AC-1001]');
            return Redirect::back();
        }
    }

    public function add()
    {
        if (!ACL::getAccsessRight($this->module_name, 'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [AC-0010]']);
        }

        try {
            $page_title = $this->module_name;
            $categories = ['' => '-- Choose category --'] + Category::where('status', 1)->pluck('name', 'id')->toArray();
            return view("Asset::asset.add", compact('page_title', 'categories'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[AC-1005]');
            return Redirect::back();
        }
    }

    public function subCategoryByCategory(Request $request)
    {
        if (!ACL::getAccsessRight($this->module_name, 'A')) {
            return CommonFunction::dataResponse(false, '',
                'You have no access right! Please contact admin for more information. [UC-0015]',
                201);
        }

        try {
            $sub_categories = SubCategory::where('cat_id', $request->cat_id)->orderBy('id', 'desc')->pluck('name', 'id')->toArray();
            if (!$sub_categories) {
                return CommonFunction::dataResponse(false, '', 'Sorry! Sub category not found in this type.', 201);
            }
            return CommonFunction::dataResponse(true, $sub_categories, 'Category wise sub category load.', 200);

        } catch (\Exception $e) {
            $data = [
                'error' => true,
                'data' => '',
                'message' => CommonFunction::showErrorPublic($e->getMessage()) . '[AC-1010]',
                'status' => 401
            ];
            return response()->json($data);
        }
    }

    public function store(Request $request)
    {
        $asset_id = (isset($request->asset_id) ? $request->asset_id : '');
        if (!ACL::getAccsessRight($this->module_name, $asset_id ? 'A' : 'UP')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [AC-0020]']);
        }

        /* validation start */
        $rules = [];
        $message = [];

        // rules
        $rules['name'] = 'required|unique:assets,name' . ($asset_id ? ",$asset_id" : '');
        $rules['cat_id'] = 'required';
        $rules['quantity'] = 'required';
        $rules['status'] = 'required';

        // custom message
        $message['name.required'] = 'Asset name is required.';
        $message['name.unique'] = $request->name . ' has already been taken. Try unique name.';
        $message['cat_id.required'] = 'Category is required.';
        $message['quantity.required'] = 'Quantity is required.';
        $message['status.required'] = 'Status is required.';

        $this->validate($request, $rules, $message);
        /* validation end */

        try {
            DB::beginTransaction();

            $assetData = Asset::findOrNew($asset_id);
            $assetData->name = $request->name;
            $assetData->cat_id = $request->cat_id;
            $assetData->sub_cat_id = $request->sub_cat_id;
            $assetData->quantity = $request->quantity;
            $assetData->status = $request->status;
            $assetData->save();

            if ((int)$assetData->quantity > 0) {
                for ($i = 0; $i < (int)$assetData->quantity; $i++) {

                    $list = new AssetList();
                    $list->asset_id = $assetData->id;
                    $list->cat_id = $assetData->cat_id;
                    $list->sub_cat_id = $assetData->sub_cat_id;
                    $list->save();

                    // Generate unique code no for tracking
                    $prefix = strtoupper(substr($request->name, 0, 2));
                    $cat_id = (strlen($request->cat_id) == 1) ? '0' . $request->cat_id : $request->cat_id;
                    $sub_cat_id = (!$request->sub_cat_id) ? '00' : ((strlen($request->sub_cat_id) == 1) ? '0' . $request->sub_cat_id : $request->sub_cat_id);
                    $code_prefix = $prefix . '-' . $cat_id . '-' . $sub_cat_id . '-';

                    DB::statement("update asset_list, asset_list as table2
                                SET asset_list.tracking_code=(select concat('$code_prefix',
                                LPAD(IFNULL(MAX(SUBSTR(table2.tracking_code, -5, 5)) + 1, 1), 5, '0')) as tracking_code
                                from (select * from asset_list) as table2
                                where table2.id!='$list->id' and table2.tracking_code like '$code_prefix%')
                                where asset_list.id='$list->id' and table2.id='$list->id'");
                }
            }

            DB::commit();

            Session::flash('success', 'Data is stored successfully!');
            return redirect()->route('asset-list');

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[AC-1015]');
            return Redirect::back()->withInput();
        }
    }

    public function edit($id)
    {
        if (!ACL::getAccsessRight($this->module_name, 'E')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [AC-0025]']);
        }
        $page_title = $this->module_name;
        try {
            $asset_by_id = Asset::with(['category', 'subCategory', 'assetList', 'updatedByUser'])->where('assets.id', $id)->first();
            return view('Asset::asset.edit', compact('page_title', 'asset_by_id'));
        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[AC-1020]');
            return Redirect::back();
        }
    }
}
