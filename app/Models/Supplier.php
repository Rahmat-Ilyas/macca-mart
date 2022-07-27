<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'tbl_supel';
    public $timestamps = false;
    protected $guarded = [];

    public function item_masuk($kode)
    {
        $get_msk = DB::table('tbl_imhd')->select('notransaksi')->where('kodesupel', $kode)->get();
        $item_masuk = 0;
        foreach ($get_msk as $dta) {
            $dtlbmsk = DB::table('tbl_imdt')->select('jumlah')->where('notransaksi', $dta->notransaksi)->get();
            foreach ($dtlbmsk as $val) {
                $get = explode('.', $val->jumlah);
                $item_masuk = $item_masuk + $get[0];
            }
        }

        return $item_masuk;
    }

    public function rt_pembelian($kode)
    {
        $get_msk = DB::table('tbl_imhd')->select('totalitem')->where('kodesupel', $kode)->get();
        $total_item = 0;
        $n = 1;
        foreach ($get_msk as $dta) {
            $get = explode('.', $dta->totalitem);
            $total_item = $total_item + $get[0];
            $n++;
        }

        return ceil($total_item / $n);
    }

    public function rt_pengeluaran($kode)
    {
        $get_msk = DB::table('tbl_imhd')->select('totalakhir')->where('kodesupel', $kode)->get();
        $totalakhir = 0;
        $n = 1;
        foreach ($get_msk as $dta) {
            $get = explode('.', $dta->totalakhir);
            $totalakhir = $totalakhir + $get[0];
            $n++;
        }

        return ceil($totalakhir / $n);
    }

    public function rt_rentangwaktu($kode)
    {
        $get_msk = DB::table('tbl_imhd')->select('dateupd')->where('kodesupel', $kode)->get();
        $gettggl = [];
        foreach ($get_msk as $dta) {
            $gettggl[] = $dta->dateupd;
        }

        $setrentang = 0;
        $n = 1;
        foreach ($gettggl as $i => $tgl) {
            if (count($gettggl) - 1 > $i) {
                $tgl1 = date_create($tgl);
                $tgl2 = date_create($gettggl[$i + 1]);
                $rentang = date_diff($tgl1, $tgl2)->days;

                $setrentang = $setrentang + $rentang;
                $n++;
            }
        }

        return (ceil($setrentang / $n) <= 0) ? 1 : ceil($setrentang / $n);
    }
}
