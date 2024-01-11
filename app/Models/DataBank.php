<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBank extends Model
{
    use HasFactory;
    protected $table = 'bank';

    public function User()
    {
        return $this->belongsTo(User::class, 'bank_id', 'id');
    }

    public function Transaksi()
    {
        return $this->hasMany(DataBank::class, 'bank_id', 'id');
    }
}
