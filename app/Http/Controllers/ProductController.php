<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    //
    public function addproduct()
    {
        $categories = Category::All()->pluck('category_name','category_name');
        return view('admin.addproduct')->with('categories',$categories);
    }
    public function products()
    {
        $products = Product::All();
        return view('admin.products')->with('products', $products);
    }
    public function saveproduct(Request $req)
    {
        $this->validate($req, [ 'product_name' => 'required',
                                'product_price' => 'required',
                                'product_category' => 'required',
                                'product_image' => 'image|nullable|max:1999']);

        if($req->hasFile('product_image')){
            //1 : get file name with exte
            $fileNameWithExt = $req->file('product_image')->getClientOriginalName();
            // 2: get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // 3: get just file extension
            $extension = $req->file('product_image')->getClientOriginalExtension();
            // 4: file name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;

            // upload image
            $path = $req->file('product_image')->storeAs('public/product_images', $fileNameToStore);


        }
        else{
            $fileNameToStore = 'noimage.jpg';
        }
        $product = new Product();
        $product->product_name = $req->input('product_name');
        $product->product_price = $req->input('product_price');
        $product->product_category = $req->input('product_category');
        $product->product_image = $fileNameToStore;
        $product->status = 1;
        $product->save();

        return back()->with('status','The product has been successfully saved!!');
    }
    public function edit_product($id)
    {
        $categories = Category::All()->pluck('category_name','category_name');
        $product = Product::find($id);

        return view('admin.edit_product')->with('product' , $product )->with('categories' , $categories);
    }
    public function updateproduct(Request $req)
    {
        $this->validate($req, [ 'product_name' => 'required',
                                'product_price' => 'required',
                                'product_category' => 'required',
                                'product_image' => 'image|nullable|max:1999']);

        $product = Product::find($req->input('id'));
        $product->product_name = $req->input('product_name');
        $product->product_price = $req->input('product_price');
        $product->product_category = $req->input('product_category');


        if($req->hasFile('product_image')){
            //1 : get file name with exte
            $fileNameWithExt = $req->file('product_image')->getClientOriginalName();
            // 2: get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // 3: get just file extension
            $extension = $req->file('product_image')->getClientOriginalExtension();
            // 4: file name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;

            // upload image
            $path = $req->file('product_image')->storeAs('public/product_images', $fileNameToStore);

            if($product->product_image != 'noimage.jpg'){
                Storage::delete('public/product_images/'.$product->product_image);
            }

            $product->product_image = $fileNameToStore;
        }


        $product->update();

        return redirect('/products')->with('status','The product has been successfully updated!!');
    }
    public function delete_product($id)
    {
        $product = Product::find($id);
        if($product->product_image != 'noimage.jpg'){
            Storage::delete('public/product_images/'.$product->product_image);
        }
        $product->delete();

        return back()->with('status','The product has been successfully deleted!!');
    }

    public function activate_product($id)
    {
        $product = Product::find($id);
        $product->status = 1;
        $product->update();

        return back()->with('status','The product has been successfully activated!!');
    }
    public function unactivate_product($id)
    {
        $product = Product::find($id);
        $product->status = 0;
        $product->update();

        return back()->with('status','The product has been successfully unactivated!!');
    }
    public function view_product_by_category($category_name)
    {
        $products = Product::All()->where('status', 1)->where('product_category',$category_name);
        $categories = Category::All();
        return view('client.shop')->with('products', $products)->with('categories', $categories);
    }

}
