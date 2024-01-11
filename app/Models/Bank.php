<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
    protected $table = 'bank';
    protected $fillable = [
        'nama_bank'
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
        // 'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
    ];

}
