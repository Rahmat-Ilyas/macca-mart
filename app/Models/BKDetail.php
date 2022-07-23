<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BKDetail extends Model
{
    use HasFactory;

    protected $table = 'tbl_ikdt';
    public $timestamps = false;
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'kodeitem', 'kodeitem');
    }
}
