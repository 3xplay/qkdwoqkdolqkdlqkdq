<?php

namespace App\Http\Controllers;

use App\Models\Game_users;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function connect_games(Request $request){
        $user = auth()->user();

        $useridnya = $user['user'];
        $users_id = $user['id'];
        $external_player_id = $user['ext_id'];
        // $gameID = $_GET['gamecode'];
        $gameID = ''; //sample

        $kode_unik = substr(str_shuffle(1234567890),0,3);
        $kd_transaksi = date('Ymds').$kode_unik.$users_id;
        $requestID = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz123456789'),0,8);
        $created_date = date('Y-m-d H:i:s');

        // game user
        $user_game = Game_users::where([
            'user_id' => $user->id,
            'provider' => 'PragmaticPlay',
            'status' => 1
        ]);

        
    }
}
