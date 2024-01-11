<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bonus;

class BonusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function depositPromotion()
    {
        $pageTitle = 'Promotion Deposit';
        $bonus = Bonus::latest()->paginate(getPaginate());
        return view('admin.settings.deposit.promotion', compact('bonus','pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function add()
    {
        $pageTitle = 'Promotion Deposit Edit';
        return view('admin.settings.deposit.add', compact('pageTitle'));
    }

    public function edit(string $id)
    {
       $pageTitle = 'Promotion Deposit Edit';
       $bonus = Bonus::find($id);
       return view('admin.settings.deposit.edit', compact('bonus','pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $input['status'] = "2"; // set default status as nonactive

            Bonus::create($input);

            return redirect()->route('backoffice.website.depositPromotion')->with('success', 'Data berhasil ditambah');
        } catch (\Exception $e) {
            return redirect()->route('backoffice.website.depositPromotion')->with('error', $e);
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
        $bonus = Bonus::find($id);
        if ($request->status != null) {
            $bonus->status = $request->status;
        } else {
            $bonus->judul = $request->judul;
            $bonus->keterangan = $request->keterangan;
            $bonus->nominal = $request->nominal;
        }

        $bonus->save();
        return redirect()->route('backoffice.website.depositPromotion')->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bonus = Bonus::find($id);
        $bonus->delete();

        return redirect()->route('backoffice.website.depositPromotion')->with('success', 'Data berhasil dihapus');
    }
}
