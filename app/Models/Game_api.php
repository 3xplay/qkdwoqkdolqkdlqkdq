<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use App\Models\Game_users;
use App\Models\Saldo;
use App\Models\Game_betting;
use App\Models\User;

/**
 * Class for generating api & prepare curl units
 */
class Game_api extends Model
{
    protected $table = 'game_api';
    private $game_api = null;

    #region connect apis
    
    function connect_curl(){
        
    }

    function game_transfer($user_id, $saldo){
        $this->pgsoft_transfer($user_id, $saldo);
    }

    #region pragmatic

    // return ext id
    
    public function create($username){
        $this->game_api_get('3xplay');
        
        $action = $this->game_api->url_request."?cmd=create&token=".$this->game_api->api_key."&username=$username";
        $responses = json_decode($this->sg_connect($action), true);

        $users = User::where('name', $username)->first();

        $game_users = new Game_users();
        $game_users->user_id = $users->id;
        $game_users->provider = $responses['data']['staging'];
        $game_users->ext_id = $responses['data']['username'];
        $game_users->player_id = $responses['data']['username'];
        $game_users->balance = 0; 
        $game_users->created_at = date('Y-m-d H:i:s');
        $game_users->status = 1; 

        $game_users->save();

        $saldo = Saldo::where([
            'user_id' => $users->id
        ])->first();

        if(empty($saldo)){
            return true;
        }

        if($saldo->saldo + $saldo->bonus == 0){
            return true;
        }
    }

    public function getbalance($username){
        $this->game_api_get('3xplay');

        $action = $this->game_api->url_request."?cmd=getbalance&token=".$this->game_api->api_key."&username=$username";
        $checkBalance = json_decode($this->sg_connect($action), true);
        
        $getUser = user::where('name', $username)->first();
        
        $data = Game_users::where('user_id', $getUser->id)->first();
        $data->balance = $checkBalance['balance'];
        $data->save();

        $saldo = Saldo::where('user_id', $getUser->id)->first();
        $saldo->save_saldo('game', $checkBalance['balance']);
        
        echo json_encode([
            'error' => false,
            'balance' => $saldo->saldo + $saldo->bonus
        ]);
    }

    public function transaksi($username,$type,$amount){ // Type Deposit & Withdraw
        $this->game_api_get('3xplay');
        $getUser = user::where('id', $username)->first();
        
        $action = $this->game_api->url_request."?cmd=transaksi&token=".$this->game_api->api_key."&username=$getUser->name&type=$type&amount=$amount";
        $Transaksi = json_decode($this->sg_connect($action), true);
        
        return $Transaksi;
    }

    public function gethistory(){
        $this->game_api_get('3xplay');

        $action = $this->game_api->url_request."?cmd=gethistory&token=".$this->game_api->api_key;
        return $this->sg_connect($action);
    }


    public function opengame($username,$gameid,$lobby,$cashier){
        global $_SERVER;
        $this->game_api_get('3xplay');

        $action = $this->game_api->url_request."?cmd=opengame&token=".$this->game_api->api_key."&username=$username&gameid=$gameid&LobbyUrl=".$_SERVER['SERVER_NAME']."&CashierUrl=".$_SERVER['SERVER_NAME'];
        $GameUrl = json_decode($this->sg_connect($action), true);
        
            if($GameUrl['status'] == "success") {
                return $GameUrl['gameUrl'];
            } else {
                return "/";
            }
        }
    

    private function sg_connect($endpoint){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE); 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }
    
    public function getGUID(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuids = 
                substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
                    
            return $uuids;
        }
    }
    #endregion pragmatic

    #region pgsoft
    
    function pgsoft_create_user(){
        DB::beginTransaction();
        $users = auth()->user();
        
        if(empty($users)){
            echo "Harus login";
            die();
        }

        $game_users = new Game_users();
        $check_exist = $game_users->check_exist('pgsoft', $users->id);

        if($check_exist !== true){
            return true;
        }

        $this->game_api_get('pgsoft');

        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $uuid = $this->pgsoft_create_uuid();
        $url = $this->game_api->url_request.'external/v3/Player/Create?trace_id='.$uuid;
     
        $datas = $this->pgsoft_curl($url, "operator_token={$this->game_api->api_key}&secret_key={$this->game_api->secret_key}&player_name={$users->name}&currency=IDR&nickname={$users->name}");      
        
        $game_users = new Game_users();
        $game_users->user_id = $users->id;
        $game_users->provider = 'pgsoft';
        $game_users->ext_id = $uuid;
        $game_users->player_id = $uuid; 
        $game_users->balance = 0; 
        $game_users->created_at = date('Y-m-d H:i:s');
        $game_users->status = 1; 

        $game_users->save();

        DB::commit();
    }

    function pgsoft_start_game($game_id){
        $users = auth()->user();

        if(empty($users)){
            echo "Harus Login";
        }

        $this->pgsoft_create_user();
        $this->pgsoft_get_balance_pp($users->id);
        $this->game_api_get('pgsoft');

        $data_login = $this->pgsoft_login();
        $ops = $data_login['token'];

        $playUrl = "https://m.pg-redirect.net/{$game_id}/index.html?ot={$this->game_api->api_key}&ops={$ops}&btt=1";
        header('Location:'.$playUrl);
        die();
        
        return $playUrl;
    }

    function pgsoft_transfer($user_id, $saldo_transfer){
        $users = User::find($user_id);


        if($saldo_transfer < 0){
            return $this->pgsoft_withdraw($user_id, abs($saldo_transfer) );
        }
        if(empty($users)){
            echo "User Not Found";
            die();
        }
        
        if(empty($users)){
            echo "Harus login";
            die();
        }

        $this->game_api_get('pgsoft');
        $player_name = $users->name;

        $kode_unik = substr(str_shuffle(1234567890),0,3);
        $kd_transaksi = date('Ymds').$kode_unik;

        $saldo = Saldo::where('user_id' , $users->id)->first();

        $amount = round($saldo_transfer / 1000, 2) ; // nominal di bagi 1000 dan 2 desimal Ex saldo 100.000 menjadi 100.00 saldo 50.000 jd 50.00

        $guid = $this->pgsoft_create_uuid();

        $url = $this->game_api->url_request.'external/Cash/v3/TransferIn?trace_id='.$guid;
        $post_fields = "operator_token={$this->game_api->api_key}&secret_key={$this->game_api->secret_key}&player_name=".$player_name."&Amount={$amount}&transfer_reference=".$kd_transaksi."&currency=IDR";

        $result = $this->pgsoft_curl($url, $post_fields);
    }

    
    function pgsoft_get_balance_pp($user_id = 0){
        $game_users = $this->pgsoft_get_all_users($user_id);

        if(empty($game_users)){
            return true;
        }

        foreach($game_users as $rows){
            $balance = $this->pgsoft_check_balance($rows->user_id);
      
            
            $data = Game_users::find($rows->id);
            $data->balance = $balance;
            $data->save();

            $saldo = Saldo::where('user_id', $rows->user_id)->first();
            $saldo->save_saldo('game', $balance);
        }

        return true;
    }

    function pgsoft_check_balance($user_id){
        $users = User::find($user_id);

        if(empty($users)){
            return false;    
        }

        $this->game_api_get('pgsoft');
        $player_name = $users->name;

        $guid = $this->pgsoft_create_uuid();

        $url_req = $this->game_api->url_request.'external/Cash/v3/GetPlayerWallet?trace_id='.$guid;
        $post_fields = "operator_token={$this->game_api->api_key}&secret_key={$this->game_api->secret_key}&player_name={$player_name}";
        
        $hasil = $this->pgsoft_curl($url_req, $post_fields);
        
        $newSaldos = $hasil['data']['totalBalance'];
        $newSaldo = $newSaldos * 1000; // Renumerasi dikali 1000 kebalikan dari waktu proses deposit balance yang dibagi 1000

        return $newSaldo;
    }

    function pgsoft_get_all_users($user_id = 0){
        $src_user = [
            'provider' => 'pgsoft',
            'status' => 1
        ];

        $src_user = $user_id === 0 ? $src_user : array_merge($src_user, ['user_id' => $user_id]);

        return Game_users::where($src_user)->get();
    }



    function pgsoft_login(){
        $users = auth()->user();
        
        if(empty($users)){
            echo "Harus login";
            die();
        }
        
        $bytes = random_bytes(20);
        $token = bin2hex($bytes);

        $url = 'https://xxi.leikesizichan.xyz/VerrifySession';
        $post_fields = "ops={$token}&playerName={$users->name}";
                            

        return [
            'result' => $this->pgsoft_curl($url, $post_fields),
            'token' => $token, 
        ];

    }

    function pgsoft_withdraw($user_id, $balance){
        $users = User::find($user_id);
        
        if(empty($users)){
            echo "Harus login";
            die();
        }
        $this->game_api_get('pgsoft');

        // chcck game user
        $game_usr = new Game_users();
        $game_user = $game_usr->check_exist('pgsoft', $user_id);
        if($game_user === false ) {
            return false;
        }
        
        $guidd = $this->pgsoft_create_uuid();
        
    
        $kode_unik = substr(str_shuffle(1234567890),0,3);
        $kd_transaksi = date('Ymds').$kode_unik;
        $requestID = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz123456789'),0,8);
    
        $url_request = $this->game_api->url_request.'external/Cash/v3/TransferAllOut?trace_id='.$guidd;
        $post_fields = "operator_token={$this->game_api->api_key}&secret_key={$this->game_api->secret_key}&player_name={$users->name}&transfer_reference=".$kd_transaksi."&currency=IDR";
        
        $this->pgsoft_curl($url_request, $post_fields);

        $saldo = Saldo::where('user_id', $user_id)->first();

        $new_saldo = ( $saldo->saldo + $saldo->bonus ) - $balance;

        $this->pgsoft_transfer($user_id, $new_saldo);

        return true;
    }

    function pgsoft_create_uuid(){
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        return 
            substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);   
    }

    
    function pgsoft_curl($url, $post_fields, $debug = false){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post_fields,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Cache-Control: no-cache"
            ),
        ));
        
        $response = curl_exec($curl);
        if($debug){
            echo "tes : <br>";
            var_dump($response);
            die();
        }

        curl_close($curl);
        return json_decode($response, true);

    }
    #endregion connect apis

    #region utility

    function game_api_get($provider){
        if(!empty($this->game_api)){
            if($this->game_api->provider == $provider){
                return true;
            }
        }

        $this->game_api = $this->where([
            'provider' => $provider,
            'status' => 1
        ])->first();

        if(empty($this->game_api)){
            echo "CHECK API : ".$provider;
            die();
        }

        return true;
    }

    #endregion
}
