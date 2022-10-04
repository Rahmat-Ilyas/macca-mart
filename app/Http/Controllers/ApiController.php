<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Sinkron;
use GrahamCampbell\ResultType\Success;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function data_sinkron()
    {
        $result = DB::table('sinkron')->orderBy('id', 'desc')->get();
        return response()->json($result);
    }

    public function tanggal_terakhir()
    {
        $tbl_accjurnal = DB::table('tbl_accjurnal')->select('tanggal')->orderBy('tanggal', 'desc')->first();
        $tbl_ikdt = DB::table('tbl_ikdt')->select('dateupd')->orderBy('dateupd', 'desc')->first();
        $tbl_ikhd = DB::table('tbl_ikhd')->select('tanggal')->orderBy('tanggal', 'desc')->first();
        $tbl_imdt = DB::table('tbl_imdt')->select('dateupd')->orderBy('dateupd', 'desc')->first();
        $tbl_imhd = DB::table('tbl_imhd')->select('tanggal')->orderBy('tanggal', 'desc')->first();
        $tbl_item = DB::table('tbl_item')->select('tanggal_add')->orderBy('tanggal_add', 'desc')->first();

        return response()->json([
            'tbl_accjurnal' => $tbl_accjurnal ? $tbl_accjurnal->tanggal : '1998-01-01',
            'tbl_ikdt' => $tbl_ikdt ? $tbl_ikdt->dateupd : '1998-01-01',
            'tbl_ikhd' => $tbl_ikhd ? $tbl_ikhd->tanggal : '1998-01-01',
            'tbl_imdt' => $tbl_imdt ? $tbl_imdt->dateupd : '1998-01-01',
            'tbl_imhd' => $tbl_imhd ? $tbl_imhd->tanggal : '1998-01-01',
            'tbl_item' => $tbl_item ? $tbl_item->tanggal_add : '1998-01-01',
        ], 200);
    }

    public function get_count_data()
    {
        $tbl_accjurnal = DB::table('tbl_accjurnal')->select('iddetail')->get()->count();
        $tbl_ikdt = DB::table('tbl_ikdt')->select('iddetail')->get()->count();
        $tbl_ikhd = DB::table('tbl_ikhd')->select('notransaksi')->get()->count();
        $tbl_imdt = DB::table('tbl_imdt')->select('iddetail')->get()->count();
        $tbl_imhd = DB::table('tbl_imhd')->select('notransaksi')->get()->count();
        $tbl_item = DB::table('tbl_item')->select('tipe')->get()->count();
        $tbl_itemjenis = DB::table('tbl_itemjenis')->select('jenis')->get()->count();
        $tbl_itemstok = DB::table('tbl_itemstok')->select('stok')->get()->count();
        $tbl_perkiraan = DB::table('tbl_perkiraan')->select('tipe')->get()->count();
        $tbl_supel = DB::table('tbl_supel')->select('tipe')->get()->count();

        return response()->json([
            'tbl_accjurnal' => $tbl_accjurnal,
            'tbl_ikdt' => $tbl_ikdt,
            'tbl_ikhd' => $tbl_ikhd,
            'tbl_imdt' => $tbl_imdt,
            'tbl_imhd' => $tbl_imhd,
            'tbl_item' => $tbl_item,
            'tbl_itemjenis' => $tbl_itemjenis,
            'tbl_itemstok' => $tbl_itemstok,
            'tbl_perkiraan' => $tbl_perkiraan,
            'tbl_supel' => $tbl_supel,
        ], 200);
    }

    public function sinkron(Request $request)
    {
        $total_data = 0;
        foreach ($request->all() as $key => $data) {
            if (is_array($data)) {
                foreach (array_chunk($data, 1000) as $i => $data_cut) {
                    foreach ($data_cut as $value) {
                        $insert = DB::table($key)->insertOrIgnore($value);
                        if ($insert) $total_data += 1;
                    }
                }
            }
        }

        Sinkron::create([
            "jumlah_data" => $total_data
        ]);

        $response = [
            "status" => "Success",
        ];
        return response()->json($response, 200);
    }
}
