<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables as DataTables;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Kategori;
use App\Models\Supplier;
use DateTime;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function home()
    {
        return view('admin/home');
    }

    public function page($page)
    {
        return view('admin/' . $page);
    }

    public function pagedir($dir = NULL, $page)
    {
        return view('admin/' . $dir . '/' . $page);
    }

    public function pagedir_id($dir = NULL, $page, $id)
    {
        return view('admin/' . $dir . '/' . $page, compact('id'));
    }
    
    public  function datatable(Request $request)
    {
        if ($request->req == 'getBarang') {
            $this->dept = $request->dept;
            if ($request->jenis == 'SEMUA') {
                $result = Barang::select('*')->orderBy('jenis', 'desc');
            } else {
                $result = Barang::select('*')->where('jenis', $request->jenis)->get();
            }


            return DataTables::of($result)->addColumn('no', function ($dta) {
                return null;
            })->addColumn('hargapokok', function ($dta) {
                $get = explode('.', $dta->hargapokok);
                return number_format($get[0]) . ',00';
            })->addColumn('hargajual1', function ($dta) {
                $get = explode('.', $dta->hargajual1);
                return number_format($get[0]) . ',00';
            })->addColumn('gdn', function ($dta) {
                $get = $dta->stokitem($dta->kodeitem, 'GDN') ? $dta->stokitem($dta->kodeitem, 'GDN')->stok : 0;
                $get = explode('.', $get);
                return $get[0];
            })->addColumn('utm', function ($dta) {
                $get = $dta->stokitem($dta->kodeitem, 'UTM') ? $dta->stokitem($dta->kodeitem, 'UTM')->stok : 0;
                $get = explode('.', $get);
                return $get[0];
            })->addColumn('action', function ($dta) {
                return '<div class="text-center">
				<button type="button" class="btn btn-secondary btn-sm waves-effect waves-light btn-detail" data-toggle1="tooltip" title="Lihat Detail" data-toggle="modal" data-target=".modal-detail" data-id="' . $dta->id . '"><i class="bx bx-detail"></i></button>
				</div>';
            })->rawColumns(['action'])->toJson();
        } else if ($request->req == 'getKategori') {
            $this->dept = $request->dept;
            $result = Kategori::select('*');

            return DataTables::of($result)->addColumn('no', function ($dta) {
                return null;
            })->addColumn('jumitem', function ($dta) {
                return $dta->jumitem($dta->jenis);
            })->toJson();
        } else if ($request->req == 'getSupplier') {
            $result = Supplier::select('*')->where('tipe', 'SU')->get();

            return DataTables::of($result)->toJson();
        } else if ($request->req == 'getPrbnSupplier') {
            $result = Supplier::select('*')->where('tipe', 'SU')->get();

            return DataTables::of($result)->addColumn('item_masuk', function ($dta) {
                return $dta->item_masuk($dta->kode) . ' Item';
            })->addColumn('rt_pembelian', function ($dta) {
                return $dta->rt_pembelian($dta->kode) . ' Item';
            })->addColumn('rt_pengeluaran', function ($dta) {
                return 'Rp.' . number_format($dta->rt_pengeluaran($dta->kode));
            })->addColumn('rt_rentangwaktu', function ($dta) {
                return 'per ' . $dta->rt_rentangwaktu($dta->kode) . ' hari';
            })->toJson();
        } else if ($request->req == 'getBarangMasuk') {
            $result = BarangMasuk::select('notransaksi', 'tanggal', 'kodekantor', 'kodesupel', 'totalitem', 'subtotal', 'tipe')->where('tipe', 'BL')->get();

            return DataTables::of($result)->addColumn('supplier', function ($dta) {
                return $dta->supplier ? $dta->supplier->nama : '-';
            })->addColumn('tanggal', function ($dta) {
                return date('d/m/Y H:i', strtotime($dta->tanggal));
            })->addColumn('jum_barang', function ($dta) {
                return count($dta->detail_barang);
            })->addColumn('totalitem', function ($dta) {
                $get = explode('.', $dta->totalitem);
                return number_format($get[0]);
            })->addColumn('subtotal', function ($dta) {
                $get = explode('.', $dta->subtotal);
                return number_format($get[0]) . ',00';
            })->addColumn('action', function ($dta) {
                return '<div class="text-center">
				<button type="button" class="btn btn-info btn-sm waves-effect waves-light btn-detail" data-toggle1="tooltip" title="Lihat Detail" data-toggle="modal" data-target=".modal-detail" data-id="' . $dta->id . '"><i class="bx bx-detail"></i></button>
				</div>';
            })->rawColumns(['action'])->toJson();
        }
    }

    public function config(Request $request)
    {
        if ($request->req == 'getGrafik')
        {
            $result = [];
            if ($request->priode == 'mingguan')
            {
                $waktu = explode('-W', $request->waktu);
                $dto = new DateTime();
                $dto->setISODate($waktu[0], $waktu[1]);
                $df = $dto->format('Y-m-d');
                $label = [];
                $data = [];
                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($df)));
                    $label[] = date('d M Y', strtotime($date));

                    $getitemklr = DB::table('tbl_ikdt')->select('dateupd')->whereDate('dateupd', $date)->get();
                    $data[] = count($getitemklr);
                }
                $title = 'Minggu ' . $waktu[1] . ', ' . $waktu[0];
            } else if ($request->priode == 'bulanan') {
                $waktu = explode('-', $request->waktu);
                $jb = cal_days_in_month(CAL_GREGORIAN, $waktu[1], $waktu[0]);
                $label = [];
                $data = [];
                for ($i = 1; $i <= $jb; $i++) {
                    $date = $request->waktu . '-' . $i;
                    $label[] = date('d M', strtotime($date));

                    $getitemklr = DB::table('tbl_ikdt')->select('dateupd')->whereDate('dateupd', $date)->get();
                    $data[] = count($getitemklr);
                }
                $title = 'Bulan ' . date('F', strtotime($date)) . ' ' . $waktu[0];
            } else if ($request->priode == 'tahunan') {
                $label = [];
                $data = [];
                for ($i = 1; $i <= 12; $i++) {
                    $date = $request->waktu . '-' . $i . '-1';
                    $label[] = date('F', strtotime($date));

                    $getitemklr = DB::table('tbl_ikdt')->select('dateupd')->whereMonth('dateupd', $i)->whereYear('dateupd', $request->waktu)->get();
                    $data[] = count($getitemklr);
                }
                $title = 'Tahun ' . $request->waktu;

            }
        }

        $result = [
            'title' => $title,
            'label' => $label,
            'data' => $data
        ];

        return response()->json($result, 200);
    }
}
