<?php

namespace App\Modules\Asset\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Modules\Asset\Models\Category;
use App\Modules\Asset\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class CategoryController extends Controller
{
    protected $module_name;

    public function __construct()
    {
        $this->module_name = 'Category';
    }

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */

    public function categoryList()
    {
        if (!ACL::getAccsessRight($this->module_name, 'R')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [CC-0005]']);
        }
        $page_title = $this->module_name;
        return view("Asset::category.list", compact('page_title'));
    }

    public function categoryGetList()
    {
        try {
            $list = Category::dataList();
            return DataTables::of($list)
                ->editColumn('sub_category', function ($list) {
                    if ($list->subCategory) {
                        $arr = [];
                        foreach ($list['subCategory'] as $sub_cat) {
                            $arr[] = $sub_cat['name'];
                        }
                        return implode(', ', $arr);
                    }
                })
                ->editColumn('status', function ($list) {
                    return CommonFunction::getStatus($list->status);
                })
                ->addColumn('action', function ($list) {
                    $html = '';
                    $html .= '<a href="' . route('asset-category-edit', ['id' => $list->id]) .
                        '" class="btn btn-primary btn-xs"> <i class="fa fa-folder-open"></i> Open </a> ';
                    return $html;
                })
                ->addIndexColumn()
                ->rawColumns(['status', 'action'])
                ->make(true);

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[CC-1001]');
            return Redirect::back();
        }
    }

    public function categoryAdd()
    {
        if (!ACL::getAccsessRight($this->module_name, 'A')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [CC-0010]']);
        }

        try {
            $page_title = $this->module_name;
            return view("Asset::category.add", compact('page_title'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[CC-1005]');
            return Redirect::back();
        }
    }

    public function categoryStore(Request $request)
    {
        $category_id = (isset($request->category_id) ? $request->category_id : '');
        if (!ACL::getAccsessRight($this->module_name, $category_id ? 'A' : 'UP')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [CC-0015]']);
        }

        /* validation start */
        $rules = [];
        $message = [];

        // rules
        $rules['name'] = 'required|unique:categories,name' . ($category_id ? ",$category_id" : '');
        $rules['status'] = 'required';

        // custom message
        $message['name.required'] = 'Category name is required.';
        $message['name.unique'] = $request->name . ' has already been taken. Try unique name.';
        $message['status.required'] = 'Status is required.';

        $this->validate($request, $rules, $message);
        /* validation end */

        try {
            DB::beginTransaction();

            $categoryData = Category::findOrNew($category_id);
            $categoryData->name = $request->name;
            $categoryData->status = $request->status;
            $categoryData->has_sub_cat = 0; // 0=no, 1=yes
            $categoryData->save();

            $sub_cat_ids = [];

            if ($request->sub_cat_name) {
                foreach ($request->sub_cat_name as $sub_cat) {

                    $subCategoryData = new SubCategory();
                    $subCategoryData->cat_id = $categoryData->id;
                    $subCategoryData->name = $sub_cat;
                    $subCategoryData->status = 1;
                    $subCategoryData->save();

                    $sub_cat_ids[] = $subCategoryData->id;
                }

                $categoryData->has_sub_cat = 1; // 0=no, 1=yes
                $categoryData->save();
            }

            if ($category_id != '') {
                SubCategory::where('cat_id', $category_id)->whereNotIn('id', $sub_cat_ids)->delete();
            }

            DB::commit();

            Session::flash('success', 'Data is stored successfully!');
            return redirect()->route('asset-category-list');

        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[CC-1010]');
            return Redirect::back()->withInput();
        }
    }

    public function categoryEdit($id)
    {
        if (!ACL::getAccsessRight($this->module_name, 'E')) {
            return Redirect::back()->with(['error' => 'You have no access right! Please contact admin for more information. [CC-0020]']);
        }

        $page_title = $this->module_name;
        try {
            $category_by_id = Category::where('id', $id)->with('subCategory')->first();
            return view('Asset::category.edit', compact('page_title', 'category_by_id'));

        } catch (\Exception $e) {
            Session::flash('error', CommonFunction::showErrorPublic($e->getMessage()) . '[CC-1015]');
            return Redirect::back();
        }
    }
}
