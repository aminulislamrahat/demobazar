<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    //
    public function addslider()
    {
        return view('admin.addslider');
    }
    public function sliders()
    {
        $sliders = Slider::All();
        return view('admin.sliders')->with('sliders', $sliders);
    }
    public function saveslider(Request $req)
    {
        $this->validate($req, [
            'description1' => 'required',
            'description2' => 'required',
            'slider_image' => 'image|nullable|max:1999|required'
        ]);


        //1 : get file name with exte
        $fileNameWithExt = $req->file('slider_image')->getClientOriginalName();
        // 2: get just file name
        $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        // 3: get just file extension
        $extension = $req->file('slider_image')->getClientOriginalExtension();
        // 4: file name to store
        $fileNameToStore = $fileName . '_' . time() . '.' . $extension;

        // upload image
        $path = $req->file('slider_image')->storeAs('public/slider_images', $fileNameToStore);



        $slider = new Slider();
        $slider->description1 = $req->input('description1');
        $slider->description2 = $req->input('description2');
        $slider->slider_image = $fileNameToStore;
        $slider->status = 1;
        $slider->save();

        return back()->with('status', 'The slider has been successfully saved!!');
    }
    public function edit_slider($id)
    {
        $slider = Slider::find($id);

        return view('admin.edit_slider')->with('slider' , $slider );
    }
    public function updateslider(Request $req)
    {
        $this->validate($req, [
            'description1' => 'required',
            'description2' => 'required',
            'slider_image' => 'image|nullable|max:1999'
        ]);

        $slider = Slider::find($req->input('id'));
        $slider->description1 = $req->input('description1');
        $slider->description2 = $req->input('description2');

        if($req->hasFile('slider_image')){
            //1 : get file name with exte
            $fileNameWithExt = $req->file('slider_image')->getClientOriginalName();
            // 2: get just file name
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            // 3: get just file extension
            $extension = $req->file('slider_image')->getClientOriginalExtension();
            // 4: file name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension;

            // upload image
            $path = $req->file('slider_image')->storeAs('public/slider_images', $fileNameToStore);

            Storage::delete('public/slider_images/'.$slider->slider_image);


            $slider->slider_image = $fileNameToStore;
        }

        $slider->update();

        return redirect('/sliders')->with('status','The slider has been successfully updated!!');
    }
    public function delete_slider($id)
    {
        $slider = Slider::find($id);

        Storage::delete('public/slider_images/'.$slider->slider_image);

        $slider->delete();

        return back()->with('status','The slider has been successfully deleted!!');
    }

    public function activate_slider($id)
    {
        $slider = Slider::find($id);
        $slider->status = 1;
        $slider->update();

        return back()->with('status','The slider has been successfully activated!!');
    }
    public function unactivate_slider($id)
    {
        $slider = Slider::find($id);
        $slider->status = 0;
        $slider->update();

        return back()->with('status','The slider has been successfully unactivated!!');
    }
}
