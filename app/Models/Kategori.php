<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'tbl_itemjenis';
    public $timestamps = false;
    protected $guarded = [];

    public function jumitem($jenis)
    {
        return count(Barang::where('jenis', $jenis)->get());
    }
}
