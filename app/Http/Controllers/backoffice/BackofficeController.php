<?php

namespace App\Http\Controllers\backoffice;

use App\Models\Transaksi;
use App\Models\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackofficeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        $pageTitle = 'Dashboard';
        
        // dashboard datas
        $trans = new Transaksi();
        $data['deposit'] = $trans->get_summary_transaksi();

        $trans = new Transaksi();
        $data['withdraw'] = $trans->get_summary_withdraw();

        $member = new User();
        $data['member'] = $member->get_summary_member();
        
        return view('admin.dashboard', $data, compact('pageTitle'));
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
