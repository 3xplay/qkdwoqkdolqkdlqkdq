<?php

namespace App\Http\Controllers\backoffice;

use App\Http\Controllers\Controller;
use App\Models\Saldo;
use App\Models\User;
use App\Models\Bank;
use App\Models\Game_api;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class PengaturanSaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function balance(Request $request)
    {
        $pageTitle = 'Balance Manage';
        
        $saldo = Saldo::where('status',1)->when(
            $request->search,
            function (Builder $builder) use ($request) {
                $builder->where('user_name', 'like', "%{$request->search}%");
            }
        )->paginate(getPaginate());
        return view('admin.balance.list', [
            'saldo' => $saldo,
            'pageTitle' => $pageTitle
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
    public function mainBall(string $id)
    {
        $pageTitle = 'Main Balance';
        $saldo = Saldo::find($id);
        $data_member = User::find($saldo->user_id);
        $data_bank = Bank::get();
        return view('admin.balance.detail',compact(['saldo','data_member','data_bank','pageTitle'], ));
    }

    public function bonusBall(string $id)
    {
        $pageTitle = 'Bonus Balance';
        $saldo = Saldo::find($id);
        $data_member = User::find($saldo->user_id);
        $data_bank = Bank::get();
        return view('admin.balance.bonus',compact(['saldo','data_member','data_bank','pageTitle'], ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($id, $request->all());
        $saldo_user = Saldo::find($id);
        $game_api = new Game_api();

        if ($request->type == 1) {
            $saldo_user->saldo += $request->nominal;
            $game_api->transaksi($saldo_user->user_id, 'deposit', $request->nominal);
        } elseif ($request->type == 2) {
            $saldo_user->saldo -= $request->nominal;
            $game_api->transaksi($saldo_user->user_id, 'withdraw', $request->nominal);
        } elseif ($request->type == 3) {
            $saldo_user->bonus += $request->nominal;
            $game_api->transaksi($saldo_user->user_id, 'deposit', $request->nominal);
        } elseif ($request->type == 4) {
            $saldo_user->bonus -= $request->nominal;
            $game_api->transaksi($saldo_user->user_id, 'withdraw', $request->nominal);
        }

        $saldo_user->save();
         // add saldo to game

        return redirect()->back()->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
