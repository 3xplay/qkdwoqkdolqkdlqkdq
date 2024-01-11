<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Themes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function setting()
    {
        $pageTitle = 'General Setting';
        $setting = Setting::first();
        return view('admin.settings.general', compact('setting','pageTitle'));
    }

    public function seo()
    {
        $pageTitle = 'SEO Setting';
        $setting = Setting::first();
        return view('admin.settings.seo', compact('setting','pageTitle'));
    }

    public function social()
    {
        $pageTitle = 'Extensions';
        $social = Setting::first();
        return view('admin.settings.social', compact('social','pageTitle'));
    }

    public function logosetting()
    {
        $pageTitle = 'Logo & Favicon Setting';
        $setting = Setting::first();
        return view('admin.settings.logo_icon', compact('setting','pageTitle'));
    }

    public function forntendthemes()
    {
        $pageTitle = 'Frontend Themes';
        $themes = Themes::all();
        $setting = Setting::first();
        return view('admin.settings.themes', compact('themes', 'setting','pageTitle'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function logoupdate(Request $request,  $id)
    {
        $logo = Setting::find($id);
        if ($request->hasFile('logo')) {

            $file_path = 'https://files.leikesizichan.skin/ImageFile/logo/' . $logo->logo;
            if (File::exists($file_path)) {
                unlink($file_path);
            }

            $path = '/ImageFile/logo/';
            $imgname = uniqid() . '_' . $request->logo->getClientOriginalName();
            Storage::disk('do')->putFileAs($path, $request->file('logo'), $imgname, 'public');

            $logo->logo = $imgname;
        }

        if ($request->hasFile('favicon')) {

            $file_path = 'https://files.leikesizichan.skin/ImageFile/logo/' . $logo->favicon;
            if (File::exists($file_path)) {
                unlink($file_path);
            }

            $path = '/ImageFile/logo/';
            $imgname = uniqid() . '_' . $request->favicon->getClientOriginalName();
            Storage::disk('do')->putFileAs($path, $request->file('favicon'), $imgname, 'public');

            $logo->favicon = $imgname;
        }

        $logo->save();
        return redirect()->back()->with('success', 'Logo changed successfully');
    }
    
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $data = Setting::find($id);
        $data->nama_web = $request->nama_web;
        $data->running_text = $request->running_text;

        $data->maintenance_mode = isset($request->maintenance_mode) ? $request->maintenance_mode : 0;

        $data->save();
        return redirect()->back()->with('success', 'Website Updated successfully');
    }

    public function updateseo(Request $request,  $id)
    {
        $data = Setting::find($id);
        $data->seo_meta_keywords = $request->seo_meta_keywords;
        $data->seo_description	 = $request->seo_description;
        $data->seo_social_title	 = $request->seo_social_title;
        $data->seo_social_description = $request->seo_social_description;

        if ($request->hasFile('seo_banner')) {

            $file_path = 'https://files.leikesizichan.skin/ImageFile/banner/' . $data->seo_banner;
            if (File::exists($file_path)) {
                unlink($file_path);
            }

            $path = '/ImageFile/banner/';
            $imgname = uniqid() . '_' . $request->seo_banner->getClientOriginalName();
            Storage::disk('do')->putFileAs($path, $request->file('seo_banner'), $imgname, 'public');

            $data->seo_banner = $imgname;
        }

        $data->save();
        return redirect()->back()->with('success', 'Seo changed successfully');
    }

    public function updatelc(Request $request,  $id)
    {
        $social = Setting::find($id);
        $social->wa = $request->wa;
        $social->tele = $request->tele;
        $social->live_chat = $request->live_chat;
        $social->live_chat_js = $request->live_chat_js;

        $social->save();
        return redirect()->back()->with('success', 'Extension changed successfully');
    }

    public function themeschange(Request $request,  $id)
    {
        $data = Setting::find($id);
        $data->themes = $request->themes;

        $data->save();
        return redirect()->back()->with('success', 'Themes changed successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
