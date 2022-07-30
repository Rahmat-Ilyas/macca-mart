<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    use HasFactory;

    protected $table = 'tbl_ikhd';
    public $timestamps = false;
    protected $guarded = [];

    public function detail_barang()
    {
        return $this->hasMany(BKDetail::class, 'notransaksi', 'notransaksi');
    }
}
