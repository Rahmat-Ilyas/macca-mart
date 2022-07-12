<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables as DataTables;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;

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

    private $dept;
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
            $this->dept = $request->dept;
            $result = Supplier::where('tipe', 'SU')->get();

            return DataTables::of($result)->toJson();
        }
    }
}
