<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\BannerPromosi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;


class BannerPromosiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function promotion()
    {
        $pageTitle = 'Promotion';
        $banner_promosi = BannerPromosi::latest()->paginate(getPaginate());
        return view('admin.settings.promotion.promotion',compact('banner_promosi','pageTitle'));
    }

    public function add()
    {
        $pageTitle = 'Promotion Add';
        return view('admin.settings.promotion.add',compact('pageTitle'));
    }

    public function edit(string $id)
    {
        $pageTitle = 'Promotion Edit';
        $banner_promosi = BannerPromosi::find($id);
        return view('admin.settings.promotion.edit', compact('banner_promosi','pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO show validation error in view
        $request->validate(
            [
                'nama' => 'required|string|max:255',
                'gambar' => 'required|image|mimes:jpeg,jpg,png|file|max:2048'
            ]
        );
        try {
            $input = $request->all();
            $input['status'] = "1"; // set default status as nonactive

            if ($request->file('gambar')) {
                $file_path = '/ImageFile/banner/';
                $imgname = uniqid() . '_' . $request->gambar->getClientOriginalName();
                Storage::disk('do')->putFileAs($file_path, $request->file('gambar'), $imgname, 'public');
                $input['gambar'] = $imgname;
            }

            BannerPromosi::create($input);

            return redirect()->route('backoffice.website.promotion')->with('success', 'Data added successfully');
        } catch (\Exception $e) {
            return redirect()->route('backoffice.website.promotion')->with('error', $e);
        }
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $banner_promosi = BannerPromosi::find($id);
        if ($request->status != null) {
            $banner_promosi->status = 1;
        } else {

            $request->validate([
                'nama' => 'required',
                'gambar' => 'image|mimes:jpeg,jpg,png|file|max:2048'
            ]);

            $banner_promosi->nama = $request->nama;
            $banner_promosi->deskripsi = $request->deskripsi;
            $banner_promosi->kategori = $request->kategori;
            $banner_promosi->batas_waktu = $request->batas_waktu;


            if ($request->file('gambar')) {
                $file_path = '/ImageFile/banner/';
                $imgname = uniqid() . '_' . $request->gambar->getClientOriginalName();
                Storage::disk('do')->putFileAs($file_path, $request->file('gambar'), $imgname, 'public');
                $banner_promosi->gambar = $imgname;
            }
        }

        $banner_promosi->save();
        return redirect()->route('backoffice.website.promotion')->with('success', 'Data changed successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $banner_promosi = BannerPromosi::find($id);
        $banner_promosi->delete();

        return redirect()->route('backoffice.website.promotion')->with('success', 'Data deleted successfully');
    }
}
