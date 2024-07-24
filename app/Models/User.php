<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class User extends Model implements AuthenticatableContract
{
    use Authenticatable;

    protected $connection = 'pgsql';
    
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'username', 'name', 'kode_bagian'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    public $incrementing = false;

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'kode_bagian', 'kode_bagian');
    }
}
