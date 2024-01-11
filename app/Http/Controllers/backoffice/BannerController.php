<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\Builder;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function banners(Request $request)
    {
        $pageTitle = 'Banners Slide';
        $banner = Banner::when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('nama', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());
        return view('admin.settings.banner.banner', compact('banner','pageTitle'));
    }

    public function editslide(string $id)
    {
        $pageTitle = 'Banners Slide Edit';
        $banner = Banner::find($id);
        return view('admin.settings.banner.edit', compact('banner','pageTitle'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function addslide()
    {
        $pageTitle = 'Banners Slide Edit';
        return view('admin.settings.banner.add', compact('pageTitle'));
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = new Banner;
        $data->nama = $request->nama;
        $data->status = $request->status;
        if ($request->hasFile('gambar')) {

            $file_path = '/ImageFile/banner/';
            $imgname = uniqid() . '_' . $request->gambar->getClientOriginalName();
            Storage::disk('do')->putFileAs($file_path, $request->file('gambar'), $imgname, 'public');
            $data->gambar = $imgname;
        }

        $data->save();
        return redirect()->route('backoffice.banners')->with('success', 'Banner added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $banner = Banner::find($id);
        if ($request->status != null) {
            $banner->status = $request->status;
        } else {
            $banner->nama = $request->nama;
            if ($request->hasFile('gambar')) {

                $file_path = '/ImageFile/banner/';
                $imgname = uniqid() . '_' . $request->gambar->getClientOriginalName();
                Storage::disk('do')->putFileAs($file_path, $request->file('gambar'), $imgname, 'public');
                $banner->gambar = $imgname;
            }
        }
        $banner->save();
        return redirect()->route('backoffice.banners')->with('success', 'Banner update successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Banner::find($id)->delete();
        return redirect()->route('backoffice.banners')->with('success', 'Banner deleted successfully');
    }
}
