<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';

    protected $table = 'role';

    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_bagian',
        'nama_bagian',
        'golongan',
    ];

    public function users()
    {
        return $this->hasMany(Employee::class, 'kode_bagian', 'kode_bagian');
    }
}
