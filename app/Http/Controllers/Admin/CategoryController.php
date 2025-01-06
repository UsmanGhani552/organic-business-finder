<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Farm;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();
        return view('admin.category.index',[
            'categories' => $categories
        ]);
    }

    public function create() {
        return view('admin.category.create');
    }

    public function store(StoreCategoryRequest $request) {
        try {
            Category::storeCategory($request->validated());
            return redirect()->route('admin.category.index')->with('success','Category Stored Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
    public function edit(Category $category) {
        return view('admin.category.edit',[
            'category' => $category
        ]);
    }

    public function update(UpdateCategoryRequest $request,Category $category) {
        try {
            Category::updateCategory($request->validated(),$category);
            return redirect()->route('admin.category.index')->with('success','Category Updated Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }

    public function delete(Category $category) {
        try {
            $category->delete();
            return redirect()->route('admin.category.index')->with('success','Category Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
