<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'tbl_supel';
    public $timestamps = false;
    protected $guarded = [];

    public function item_masuk($kode)
    {
        $get_msk = BarangMasuk::where('kodesupel', $kode)->get();
        $item_masuk = 0;
        foreach ($get_msk as $dta) {
            $item_masuk = $item_masuk + count($dta->detail_barang);
        }

        return $item_masuk;
    }
}
