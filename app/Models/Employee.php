<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model implements AuthenticatableContract
{
    use HasFactory, Authenticatable;

    protected $connection = 'pgsql';

    protected $table = 'employee';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_bagian',
        'name',
        'username',
        'golongan',
        'is_active',
        'password',
        'email_verified_at',
        'email',
        'remember_token',
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

    public function bagian()
    {
        return $this->belongsTo(Role::class, 'kode_bagian', 'kode_bagian');
    }
}
