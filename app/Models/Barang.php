<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'tbl_item';
    public $timestamps = false;
    protected $guarded = [];

    public function stokitem($kodeitem, $dept)
    {
        return Stok::where('kodeitem', $kodeitem)->where('kantor', $dept)->first();
    }
}
