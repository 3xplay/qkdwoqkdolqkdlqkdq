<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function DataBank()
    {
        return $this->belongsTo(DataBank::class, 'bank_id', 'id');
    }

    #region dashboard

    function get_summary_transaksi(){
        $date_yest = date("Y-m-d", strtotime(date('Y-m-d') ." -1 day") );

        $src_today = " BETWEEN '".date('Y-m-d 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";
        $src_yesterday = " BETWEEN '".date($date_yest.' 00:00:00')."' AND '".date($date_yest.' 23:59:59')."'";
        $src_month = " BETWEEN '".date('Y-m-01 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";
        $src_all = " BETWEEN '".date('2000 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";

        $sql = "SELECT 
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_today}
                    AND trans.status = 2
            ),0) as trans_now,
            IFNULL((
                SELECT
                    COUNT(trans.id)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_today}
                    AND trans.status = 2
            ),0) as trans_count,
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_yesterday}
                    AND trans.status = 2
            ),0) as trans_yesterday,
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_month}
                    AND trans.status = 2
            ),0) as trans_month,
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_all}
                    AND trans.status = 2
            ),0) as trans_all,
            IFNULL((
                SELECT
                   COUNT(trans.id)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_all}
                    AND trans.status = 3
            ),0) as trans_reject,
            IFNULL((
                SELECT
                   COUNT(trans.id)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_all}
                    AND trans.status = 1
            ),0) as trans_pend,
            IFNULL((
                SELECT
                   COUNT(trans.id)
                FROM
                    transaksi trans
                WHERE
                    trans.approved_at {$src_all}
                    AND trans.status = 2
            ),0) as trans_done
            ";

        $query = DB::select($sql)[0];

        return $query;
    }

    function get_summary_withdraw(){
        $date_yest = date("Y-m-d", strtotime(date('Y-m-d') ." -1 day") );

        $src_today = " BETWEEN '".date('Y-m-d 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";
        $src_yesterday = " BETWEEN '".date($date_yest.' 00:00:00')."' AND '".date($date_yest.' 23:59:59')."'";
        $src_month = " BETWEEN '".date('Y-m-01 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";
        $src_all = " BETWEEN '".date('2000 00:00:00')."' AND '".date('Y-m-d 23:59:59')."'";

        $sql = "SELECT 
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.type = 2
                    AND trans.status = 2
            ),0) as trans_now,
            IFNULL((
                SELECT
                    COUNT(trans.id)
                FROM
                    transaksi trans
                WHERE
                    trans.type = 2
                    AND trans.status = 2
            ),0) as trans_count,
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.type = 2
                    AND trans.status = 2
            ),0) as trans_yesterday,
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.type = 2
                    AND trans.status = 2
            ),0) as trans_month,
            IFNULL((
                SELECT
                    SUM(trans.nominal)
                FROM
                    transaksi trans
                WHERE
                    trans.type = 2
                    AND trans.status = 2
            ),0) as trans_all,
            IFNULL((
                SELECT
                   COUNT(trans.id)
                FROM
                    transaksi trans
                WHERE
                    trans.type = 2
                    AND trans.status = 3
            ),0) as trans_reject,
            IFNULL((
                SELECT
                   COUNT(trans.id)
                FROM
                    transaksi trans
                WHERE
                    trans.type = 2
                    AND trans.status = 1
            ),0) as trans_pend
            ";

        $query = DB::select($sql)[0];

        return $query;
    }
    #endregion
    #endregion
}
