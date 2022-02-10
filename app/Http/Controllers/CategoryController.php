<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    //
    public function addcategory()
    {
        return view('admin.addcategory');
    }

    public function categories()
    {
        $categories = Category::All();
        return view('admin.categories')->with('categories' , $categories);
    }
    public function savecategory(Request $req)
    {
        $this->validate($req, ['category_name' => 'required|unique:categories']);

        $category = new Category();
        $category->category_name = $req->input('category_name');
        $category->save();

        return back()->with('status','The category name has been successfully saved!!');
    }
    public function edit_category($id)
    {
        $category = Category::find($id);
        return view('admin.edit_category')->with('category' , $category);
    }
    public function updatecategory(Request $req)
    {
        $this->validate($req, ['category_name' => 'required|unique:categories']);

        $category = Category::find($req->input('id'));
        $category->category_name = $req->input('category_name');
        $category->update();

        return redirect('/categories')->with('status','The category name has been successfully updated!!');
    }
    public function delete_category($id)
    {
        $category = Category::find($id)->delete();
        return back()->with('status','The category has been successfully deleted!!');
    }
}
