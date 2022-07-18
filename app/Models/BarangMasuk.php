<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    use HasFactory;

    protected $table = 'tbl_imhd';
    public $timestamps = false;
    protected $guarded = [];

    public function detail_barang()
    {
        return $this->hasMany(BMDetail::class, 'notransaksi', 'notransaksi');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'kodesupel', 'kode');
    }
}
