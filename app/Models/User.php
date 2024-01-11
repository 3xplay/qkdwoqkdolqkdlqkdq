<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'telp',
        'ref_code',
        'nama_rek',
        'bank',
        'no_rek',
        'game_mode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function DataBank()
    {
        return $this->hasMany(DataBank::class, 'bank_id', 'id');
    }

    public function Transaksi()
    {
        return $this->hasMany(DataBank::class, 'user_id', 'id');
    }
    public function Saldo()
    {
        return $this->hasMany(Saldo::class, 'user_id', 'id');
    }

    #region dashboard

    function get_summary_member(){
        $date_yest = date("Y-m-d", strtotime(date('Y-m-d') ." -1 day") );

        $src_today = " BETWEEN '".date('Y-m-d 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";
        $src_yesterday = " BETWEEN '".date($date_yest.' 00:00:00')."' AND '".date($date_yest.' 23:59:59')."'";
        $src_month = " BETWEEN '".date('Y-m-01 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";
        $src_all = " BETWEEN '".date('2000 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";

        $sql = "SELECT 
            IFNULL((
                SELECT
                    COUNT(usr.id)
                FROM
                    users usr
                WHERE
                    usr.created_at {$src_today}
                    AND usr.level IS NULL
                    
            ),0) as trans_now,
            IFNULL((
                SELECT
                    COUNT(usr.id)
                FROM
                    users usr
                WHERE
                    usr.created_at {$src_yesterday}
                    AND usr.level IS NULL
                    
            ),0) as trans_yesterday,
            IFNULL((
                SELECT
                    COUNT(usr.id)
                FROM
                    users usr
                WHERE
                    usr.created_at {$src_month}
                    AND usr.level IS NULL
                    
            ),0) as trans_month,
            IFNULL((
                SELECT
                    COUNT(usr.id)
                FROM
                    users usr
                WHERE
                    usr.created_at {$src_all}
                    AND usr.level IS NULL
                    
            ),0) as trans_all
            ";

        $query = DB::select($sql)[0];

        return $query;
    }

    public function user($src, $pages = 1){
        $pages = $this->limit * ($pages - 1);

        $src_where = "";
        if(!empty($src)){
            $src_where = "WHERE
            ( 
                name like '%{$src}%' 
                OR telp like '%{$src}%'
                OR email like '%{$src}%'
            )";
        }
        $sql = "SELECT 
                id, name, email, users.email, created_at, nama_rek, bank
            FROM
                users 
            {$src_where}
            LIMIT {$this->limit}
            OFFSET {$pages}";

        $query = DB::select($sql);

        return $query;
    }
    #endregion
}
