<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\DataBank;
use App\Models\Saldo;
use App\Models\Transaksi;
use App\Models\Game_api;
use App\Models\Game_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class DepositController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function deposit(Request $request)
    {

        $pageTitle = 'Deposits List';

        $date_start = empty($request->date_start) ? date('Y-m-d', strtotime(date('Y-m-d').' -3 day')) : addslashes($request->date_start);
        $date_end = empty($request->date_end) ? date('Y-m-d') : addslashes($request->date_end);

        $status = empty($request->status) ? 1 : $request->status;
        $status = is_numeric($status) ? $status :  1;
        
        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 1)->whereBetween('created_at', [$date_start.' 00:00:00', $date_end.' 23:59:59']);
        
        if($status > 0){
            $deposit = $deposit->where('status', $status);
        }
        $deposit = $deposit->orderBy('id', 'desc')->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('user_name', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());

        $data_view = [
            'date_start' => $date_start,
            'date_end' => $date_end,
            'deposit' => $deposit,
        ];

        return view('admin.deposit.log', $data_view, compact('pageTitle'));
    }

    public function approved(Request $request)
    {
        $pageTitle = 'Deposits Approved';

        $status = 2;
        
        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 1);
        
        if($status > 0){
            $deposit = $deposit->where('status', $status);
        }
        $deposit = $deposit->orderBy('id', 'desc')->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('user_name', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());

        $data_view = [
            'deposit' => $deposit,
        ];

        return view('admin.deposit.approved', $data_view, compact('pageTitle'));
    }

    public function rejected(Request $request)
    {
        $pageTitle = 'Deposits Rejected';

        $status = 3;
        
        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 1);
        
        if($status > 0){
            $deposit = $deposit->where('status', $status);
        }
        $deposit = $deposit->orderBy('id', 'desc')->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('user_name', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());

        $data_view = [
            'deposit' => $deposit,
        ];

        return view('admin.deposit.rejected', $data_view, compact('pageTitle'));
    }

    public function alldeposit(Request $request)
    {
        $pageTitle = 'Deposits History';
        
        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 1);
        
        $deposit = $deposit->orderBy('id', 'desc')->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('user_name', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());

        $data_view = [
            'deposit' => $deposit,
        ];

        return view('admin.deposit.list', $data_view, compact('pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function details(Request $request, string $id)
    {   

        $pageTitle = 'Deposits Details';
        $deposit = Transaksi::find($id);

        return view('admin.deposit.detail',compact(['deposit', 'pageTitle'] ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function approve(string $id)
    {
        $deposit = Transaksi::find($id);
        $deposit->status = 2;
        $deposit->approved_at = date('Y-m-d H:i:s');
        $deposit->approved_by = auth()->user()->name;

        $deposit->save();

        $bonus = $deposit->nominal * $deposit->bonus_persentase / 100;
            
        $saldo = Saldo::where('user_id', $deposit->user_id)->first();

        $saldo->saldo = $saldo->saldo + $deposit->nominal;
        $saldo->bonus = $saldo->bonus + $bonus;

        $game_api = new Game_api();
        $game_api->transaksi($deposit->user_id, 'deposit', $deposit->nominal + $bonus);
       
        $deposit->save();

        return redirect()->route('backoffice.deposit')->with('success', 'Deposit Approved successfully');
    }

    public function reject(Request $request, string $id)
    {
        $deposit = Transaksi::find($id);
        $deposit->status = 3;
        $deposit->alasan = $request->alasan;
        $deposit->approved_at = date('Y-m-d H:i:s');
        $deposit->approved_by = auth()->user()->name;

        $deposit->save();

        $saldo = Saldo::where('user_id', $deposit->user_id)->first();
            $saldo->saldo = $saldo->saldo + $deposit->nominal;
            $saldo->save();

            $game_api = new Game_api();
            $game_api->game_transfer($deposit->user_id, $deposit->nominal);

        return redirect()->route('backoffice.deposit')->with('success', 'Deposit Rejected successfully');
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
