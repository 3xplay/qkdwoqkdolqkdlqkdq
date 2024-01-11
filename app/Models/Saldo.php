<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Saldo_log;
use Illuminate\Support\Facades\DB;
class Saldo extends Model
{
    use HasFactory;
    protected $table = 'saldo';
    private $limit = 9999999999;

    /**
     * Save saldo with log
     * @param string $type - Trans | Game
     */
    public function save_saldo($type = 'trans', $saldo_new = 0){
        $saldo_now = $this->saldo;
        $bonus_now = $this->bonus;

        if($saldo_new == ($saldo_now + $bonus_now)){
            return true;
        }

        // add saldo dari bets
        if($saldo_new > ($saldo_now + $bonus_now)){
            $this->saldo = $saldo_new - $bonus_now;
        }else{
            // dikurangi bonus terlebih dahulu
            $this->bonus = $saldo_new <= $this->saldo ? 0 : $saldo_new - $saldo_now;
            $this->saldo = $this->bonus > 0 ? $this->saldo : $this->saldo + ($saldo_new - $saldo_now); // saldo masih sama
        }
        
        $saldo_logger = new Saldo_log();

        $saldo_logger->saldo_id = $this->id;
        $saldo_logger->user_id = $this->user_id;

        $saldo_logger->saldo_before = $saldo_now;
        $saldo_logger->saldo_trans = $this->saldo - $saldo_now;
        $saldo_logger->saldo_after = $this->saldo;

        $saldo_logger->bonus_before = $bonus_now;
        $saldo_logger->bonus_trans = $this->bonus - $bonus_now;
        $saldo_logger->bonus_after = $this->bonus;

        $saldo_logger->type = $type;
        $saldo_logger->save();
        
        parent::save();
        
    }

    public function saldo_list(){

        $src_where = "";
        if(!empty($src)){
            $src_where = "WHERE
            ( 
                users.name like '%{$src}%' 
                OR users.telp like '%{$src}%'
                OR users.email like '%{$src}%'
            )";
        }
        $sql = "SELECT 
                saldo.id, users.name, users.telp, users.email, saldo.user_id, saldo.saldo, saldo.bonus
            FROM
                saldo 
            JOIN
                users 
                ON users.id = saldo.user_id
            {$src_where}
            LIMIT {$this->limit}";

        $query = DB::select($sql);

        return $query;
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
