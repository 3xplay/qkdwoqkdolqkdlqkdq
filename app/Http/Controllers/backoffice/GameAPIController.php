<?php

namespace App\Http\Controllers\backoffice;

use App\Models\Setting;
use App\Models\Game_list;
use App\Models\Game_api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

class GameAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $game_api = Game_api::where('status', 1)->get();
        return view('backoffice.games.game_api', 
        [
            'game_api' => $game_api
        ]);
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
        $datas = Game_api::find($request->id);

        $datas->provider_name = $request->provider_name;

        $datas->api_key = $request->api_key;
        $datas->secret_key = $request->secret_key;
        $datas->url_request = $request->url_request;

        $datas->save();
        
        return redirect()->back()->with('success', 'Data berhasil diubah');
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
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
