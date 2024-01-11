<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\DataBank;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DepositbankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function gateway()
    {
        $pageTitle = 'Payment Gateways';
        $bank = DataBank::all();
        // dd($bank);
        return view('admin.gateway.list', compact('bank','pageTitle'));
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pageTitle = 'Payment Gateways Edit';
        $bank = DataBank::find($id);
        // dd($bank);
        return view('admin.gateway.edit', compact('bank','pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $bank = DataBank::find($id);
        if ($request->status != null) {
            $bank->status = $request->status;
        } else {
            $bank->nama_bank = $request->bank;
            $bank->nama_penerima = $request->nama;
            $bank->no_rek = $request->no;
        }
        
        if ($request->hasFile('qr')) {
            $file_path = '/ImageFile/Qris/';
            $imgname = uniqid() . '_' . $request->qr->getClientOriginalName();
            Storage::disk('do')->putFileAs($file_path, $request->file('qr'), $imgname, 'public');

            $bank->image_qr = $imgname;
        }
        $bank->save();
        return redirect()->route('backoffice.gateway')->with('success', 'Bank Update Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
