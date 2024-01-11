<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\Saldo;
use App\Models\Game_api;
use Illuminate\Database\Eloquent\Builder;

class WithdrawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function withdrawal(Request $request)
    {

        $pageTitle = 'Pending Withdrawal';

        $date_start = empty($request->date_start) ? date('Y-m-d', strtotime(date('Y-m-d').' -3 day')) : addslashes($request->date_start);
        $date_end = empty($request->date_end) ? date('Y-m-d') : addslashes($request->date_end);

        $status = empty($request->status) ? 1 : $request->status;
        $status = is_numeric($status) ? $status :  1;

        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 2)->whereBetween('created_at', [$date_start.' 00:00:00', $date_end.' 23:59:59']);        
        
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
            'status' => $status,
            'deposit' => $deposit
        ];
        
        return view('admin.withdraw.pending', $data_view, compact('pageTitle'));
    }

    public function approved(Request $request)
    {

        $pageTitle = 'Approved Withdrawal';

        $date_start = empty($request->date_start) ? date('Y-m-d', strtotime(date('Y-m-d').' -3 day')) : addslashes($request->date_start);
        $date_end = empty($request->date_end) ? date('Y-m-d') : addslashes($request->date_end);

        $status = 2;

        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 2)->whereBetween('created_at', [$date_start.' 00:00:00', $date_end.' 23:59:59']);        
        
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
            'status' => $status,
            'deposit' => $deposit
        ];
        
        return view('admin.withdraw.approved', $data_view, compact('pageTitle'));
    }

    public function rejected(Request $request)
    {

        $pageTitle = 'Rejected Withdrawal';

        $date_start = empty($request->date_start) ? date('Y-m-d', strtotime(date('Y-m-d').' -3 day')) : addslashes($request->date_start);
        $date_end = empty($request->date_end) ? date('Y-m-d') : addslashes($request->date_end);

        $status = 3;

        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 2)->whereBetween('created_at', [$date_start.' 00:00:00', $date_end.' 23:59:59']);        
        
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
            'status' => $status,
            'deposit' => $deposit
        ];
        
        return view('admin.withdraw.rejected', $data_view, compact('pageTitle'));
    }

    public function list(Request $request)
    {
        $pageTitle = 'Withdrawal History';
        
        $deposit = Transaksi::with('User')->with('DataBank')->where('type', 2);
        
        $deposit = $deposit->orderBy('id', 'desc')->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('user_name', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());

        $data_view = [
            'deposit' => $deposit,
        ];

        return view('admin.withdraw.list', $data_view, compact('pageTitle'));
    }

    public function details(Request $request, string $id)
    {   
        $pageTitle = 'Withdrawal Detail';
        $withdraw = Transaksi::find($id);

        return view('admin.withdraw.detail',compact(['withdraw'], 'pageTitle' ));
    }

    public function approve(string $id)
    {
        $withdraw = Transaksi::find($id);
        $withdraw->status = 2;
        $withdraw->approved_at = date('Y-m-d H:i:s');
        $withdraw->approved_by = auth()->user()->name;

        $withdraw->save();


        return redirect()->route('backoffice.withdrawal.list')->with('success', 'Withdrawal Approved successfully');
    }

    public function reject(Request $request, string $id)
    {
        $withdraw = Transaksi::find($id);
        $withdraw->status = 3;
        $withdraw->alasan = $request->alasan;
        $withdraw->approved_at = date('Y-m-d H:i:s');
        $withdraw->approved_by = auth()->user()->name;

        $withdraw->save();

        $saldo = Saldo::where('user_id', $withdraw->user_id)->first();
            $saldo->saldo = $saldo->saldo + $withdraw->nominal;
            $saldo->save();

            $game_api = new Game_api();
            $game_api->transaksi($withdraw->user_id, 'deposit', $withdraw->nominal);

        return redirect()->route('backoffice.withdrawal.list')->with('success', 'Withdrawal Rejected successfully');
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
    public function aksi(string $id, Request $request)
    {
        $transaksi = Transaksi::find($id);
        $transaksi->status = $request->status;
        $transaksi->save();
        
        return redirect()->back()->with('success', 'Data changed successfully');
    }
}
