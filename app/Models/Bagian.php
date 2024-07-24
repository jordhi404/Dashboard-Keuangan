<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    use HasFactory;

    protected $connection = 'pgsql';

    protected $table = 'bagians';

    protected $primaryKey = 'id';

    public function users()
    {
        return $this->hasMany(User::class, 'kode_bagian', 'kode_bagian');
    }
}
