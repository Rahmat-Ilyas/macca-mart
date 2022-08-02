<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

use App\Models\Barang;
use App\Models\BarangKeluar;
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
            if ($request->jenis == 'SEMUA') {
                $result = Barang::select('*')->orderBy('jenis', 'desc');
            } else {
                $result = Barang::select('*')->where('jenis', $request->jenis);
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
            $result = Supplier::select('*')->where('tipe', 'SU');

            return DataTables::of($result)->toJson();
        } else if ($request->req == 'getPrbnSupplier') {
            $result = Supplier::select('*')->where('tipe', 'SU');

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
            if ($request->lokasi == 'ALL') {
                $get_kantor = DB::table('tbl_imhd')->select('notransaksi', 'tanggal', 'kodekantor', 'kodesupel', 'totalitem', 'subtotal', 'tipe')->where('tipe', 'BL');
            } else if ($request->lokasi == 'UTM') {
                $get_kantor = DB::table('tbl_imhd')->select('notransaksi', 'tanggal', 'kodekantor', 'kodesupel', 'totalitem', 'subtotal', 'tipe')->where('tipe', 'BL')->where('kodekantor', 'UTM');
            } else {
                $get_kantor = DB::table('tbl_imhd')->select('notransaksi', 'tanggal', 'kodekantor', 'kodesupel', 'totalitem', 'subtotal', 'tipe')->where('tipe', 'BL')->where('kodekantor', 'GDN');
            }

            if ($request->priode == 'harian') {
                $result = $get_kantor->whereDate('tanggal', $request->waktu);
            } else if ($request->priode == 'bulanan') {
                $waktu = explode('-', $request->waktu);
                $result = $get_kantor->whereMonth('tanggal', $waktu[1])->whereYear('tanggal', $waktu[0]);
            } else if ($request->priode == 'tahunan') {
                $result = $get_kantor->whereYear('tanggal', $request->waktu);
            }

            return DataTables::of($result)->addColumn('supplier', function ($dta) {
                $supplier = DB::table('tbl_supel')->select('nama', 'kode')->where('kode', $dta->kodesupel)->first();
                return $supplier ? $supplier->nama : '-';
            })->addColumn('tanggal', function ($dta) {
                return date('d/m/Y H:i', strtotime($dta->tanggal));
            })->addColumn('jum_barang', function ($dta) {
                $jumlh = DB::table('tbl_imdt')->select('notransaksi')->where('notransaksi', $dta->notransaksi)->get();
                return count($jumlh) . ' Item';
            })->addColumn('totalitem', function ($dta) {
                return round($dta->totalitem) . ' Item';
            })->addColumn('subtotal', function ($dta) {
                return 'Rp.' . number_format(round($dta->subtotal)) . ',00';
            })->addColumn('action', function ($dta) {
                return '<div class="text-center">
				<button type="button" class="btn btn-info btn-sm waves-effect waves-light btn-detail" data-toggle1="tooltip" title="Lihat Detail" data-bs-toggle="modal" data-bs-target=".modal-detail" data-id="' . $dta->notransaksi . '"><i class="bx bx-detail"></i></button>
				</div>';
            })->rawColumns(['action'])->toJson();
        } else if ($request->req == 'getDataTransaksi') {
            $barang = DB::table('tbl_ikhd')->select('notransaksi', 'tanggal', 'tipe', 'totalitem', 'totalakhir', 'jmltunai', 'user1')->where('tipe', 'KSR');

            if ($request->priode == 'harian') {
                $result = $barang->whereDate('tanggal', $request->waktu);
            } else if ($request->priode == 'bulanan') {
                $waktu = explode('-', $request->waktu);
                $result = $barang->whereMonth('tanggal', $waktu[1])->whereYear('tanggal', $waktu[0]);
            } else if ($request->priode == 'tahunan') {
                $result = $barang->whereYear('tanggal', $request->waktu);
            }

            return DataTables::of($result)->addColumn('tanggal', function ($dta) {
                return date('d/m/Y H:i', strtotime($dta->tanggal));
            })->addColumn('jum_barang', function ($dta) {
                $jumlh = DB::table('tbl_ikdt')->select('notransaksi')->where('notransaksi', $dta->notransaksi)->get();
                return count($jumlh) . ' Item';
            })->addColumn('totalitem', function ($dta) {
                return round($dta->totalitem) . ' Item';
            })->addColumn('totalakhir', function ($dta) {
                return 'Rp.' . number_format(round($dta->totalakhir)) . ',00';
            })->addColumn('jmltunai', function ($dta) {
                return 'Rp.' . number_format(round($dta->jmltunai)) . ',00';
            })->addColumn('action', function ($dta) {
                return '<div class="text-center">
				<button type="button" class="btn btn-info btn-sm waves-effect waves-light btn-detail" data-toggle1="tooltip" title="Lihat Detail" data-bs-toggle="modal" data-bs-target=".modal-detail" data-id="' . $dta->notransaksi . '"><i class="bx bx-detail"></i></button>
				</div>';
            })->rawColumns(['action'])->toJson();
        } else if ($request->req == 'getJurnal') {
            $jurnal = DB::table('tbl_accjurnal')->select('*')->orderBy('tanggal', 'DESC')->orderBy('nourut', 'ASC');

            if ($request->modul == 'BLI') {
                $jurnal = $jurnal->where('modul', 'BLI');
            } else if ($request->modul == 'JUA') {
                $jurnal = $jurnal->where('modul', 'JUA');
            } else {
                $jurnal = $jurnal->where('modul', 'PRS');
            }

            if ($request->priode == 'harian') {
                $result = $jurnal->whereDate('tanggal', $request->waktu);
            } else if ($request->priode == 'bulanan') {
                $waktu = explode('-', $request->waktu);
                $result = $jurnal->whereMonth('tanggal', $waktu[1])->whereYear('tanggal', $waktu[0]);
            } else if ($request->priode == 'tahunan') {
                $result = $jurnal->whereYear('tanggal', $request->waktu);
            }

            return DataTables::of($result)->addColumn('tanggal', function ($dta) {
                return date('d/m/Y H:i', strtotime($dta->tanggal));
            })->addColumn('nama_akun', function ($dta) {
                $akun = DB::table('tbl_perkiraan')->where('kodeacc', $dta->kodeacc)->first();
                return $akun->namaacc;
            })->addColumn('debet', function ($dta) {
                return 'Rp.' . number_format($dta->debet, 2, ',', '.');
            })->addColumn('kredit', function ($dta) {
                return 'Rp.' . number_format($dta->kredit, 2, ',', '.');
            })->toJson();
        } else if ($request->req == 'getFrBarang') {
            $result = DB::table('tbl_item')->join('tbl_itemstok', 'tbl_itemstok.kodeitem', '=', 'tbl_item.kodeitem')->selectRaw('tbl_item.*, SUM(tbl_itemstok.stok) as total_stok')->groupBy('tbl_item.kodeitem')->orderBy('total_stok', 'DESC');
            if ($request->jenis != 'SEMUA') {
                $result = $result->where('jenis', $request->jenis);
            }

            return DataTables::of($result)->addColumn('no', function ($dta) {
                return null;
            })->addColumn('stok_gu', function ($dta) {
                $gdn = DB::table('tbl_itemstok')->where('kodeitem', $dta->kodeitem)->where('kantor', 'GDN')->first();
                $gdn = $gdn ? round($gdn->stok) : '0';
                $utm = DB::table('tbl_itemstok')->where('kodeitem', $dta->kodeitem)->where('kantor', 'UTM')->first();
                $utm = $utm ? round($utm->stok) : '0';

                return $gdn . ' / ' . $utm . ' (' . $dta->satuan . ')';
            })->addColumn('total_stok', function ($dta) {
                return round($dta->total_stok) . ' ' . $dta->satuan;
            })->addColumn('stokmin', function ($dta) {
                return round($dta->stokmin) . ' ' . $dta->satuan;
            })->addColumn('action', function ($dta) {
                return '<div class="text-center">
				<button type="button" class="btn btn-secondary btn-sm waves-effect waves-light btn-detail" data-toggle1="tooltip" title="Lihat Detail" data-toggle="modal" data-target=".modal-detail" data-id="' . $dta->kodeitem . '"><i class="bx bx-detail"></i></button>
				</div>';
            })->rawColumns(['action'])->toJson();
        }
    }

    public function config(Request $request)
    {
        if ($request->req == 'getGrafik') {
            $result = [];
            if ($request->priode == 'mingguan') {
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

            $result = [
                'title' => $title,
                'label' => $label,
                'data' => $data
            ];

            return response()->json($result, 200);
        } else if ($request->req == 'getProdukLaku') {
            $data = [];
            $produk = DB::table('tbl_ikdt')->selectRaw('kodeitem, SUM(jumlah) as jumlah, SUM(total) as total')->groupBy('kodeitem');
            if ($request->priode == 'harian') {
                $produk = $produk->whereDate('dateupd', $request->waktu)->orderBy('jumlah', 'DESC')->limit(10)->get();
                $title = 'Tanggal ' . date('d F Y', strtotime($request->waktu));
            } else if ($request->priode == 'bulanan') {
                $waktu = explode('-', $request->waktu);
                $produk = $produk->whereMonth('dateupd', $waktu[1])->whereYear('dateupd', $waktu[0])->orderBy('jumlah', 'DESC')->limit(10)->get();
                $title = 'Bulan ' . date('F', strtotime($request->waktu . '-1')) . ' ' . $waktu[0];
            } else if ($request->priode == 'tahunan') {
                $produk = $produk->whereYear('dateupd', $request->waktu)->orderBy('jumlah', 'DESC')->limit(10)->get();
                $title = 'Tahun ' . $request->waktu;
            }

            if ($request->priode == 'all') {
                // $produk = DB::table('tbl_ikdt')->join('tbl_item', 'tbl_ikdt.kodeitem', '=', 'tbl_item.kodeitem')->selectRaw('tbl_ikdt.kodeitem, SUM(tbl_ikdt.jumlah) as jumlah, SUM(tbl_ikdt.total) as total')->groupBy('tbl_ikdt.kodeitem');
                // $produk = $produk->orderBy('jumlah', 'ASC')->limit(500)->get();

                $produk = DB::table('tbl_item')->select('kodeitem')->get();
                foreach ($produk as $i => $dta) {
                    $get_jum = DB::table('tbl_ikdt')->select('kodeitem', 'jumlah')->where('kodeitem', $dta->kodeitem)->get();
                    $jumlah = 0;
                    foreach ($get_jum as $jum) {
                        $jumlah += round($jum->jumlah);
                    }
                    $nama = Barang::where('kodeitem', $dta->kodeitem)->first()->namaitem;
                    $data[$i]['kodeitem'] = $dta->kodeitem;
                    $data[$i]['nama'] = $nama;
                    $data[$i]['jumlah'] = $jumlah;
                }
                $jml = array_column($data, 'jumlah');
                array_multisort($jml, SORT_ASC, $data);

                $title = '';
            } else {
                foreach ($produk as $i => $dta) {
                    $nama = Barang::where('kodeitem', $dta->kodeitem)->first()->namaitem;
                    $data[$i]['kodeitem'] = $dta->kodeitem;
                    $data[$i]['nama'] = $nama;
                    $data[$i]['jumlah'] = round($dta->jumlah);
                    $data[$i]['total'] = 'Rp.' . number_format(round($dta->total));
                }
            }

            if ($request->get == 'plLaku') $n = 10;
            else $n = 500;

            $result = [];
            $color = [];
            $label = [];
            $series = [];
            $data_fix = [];
            for ($i = 0; $i < $n; $i++) {
                if (count($data) > 0) {
                    $rand = str_pad(dechex(rand(0x000000, 0xffffff)), 6, 0, STR_PAD_LEFT);
                    $color[] = "#" . $rand;
                    $label[] = $data[$i]['nama'];
                    $series[] = $data[$i]['jumlah'];
                    $data_fix[] = $data[$i];
                }
            }

            $result = [
                "title" => $title,
                "color" => $color,
                "label" => $label,
                "series" => $series,
                "data" => $data_fix,
            ];

            return response()->json($result, 200);
        } else if ($request->req == 'getKategoriLaku') {
            $data = [];
            $kategori = DB::table('tbl_ikdt')->join('tbl_item', 'tbl_ikdt.kodeitem', '=', 'tbl_item.kodeitem')->selectRaw('tbl_item.jenis, SUM(tbl_ikdt.jumlah) as jmlh, SUM(tbl_ikdt.total) as total')->groupBy('tbl_item.jenis');
            if ($request->priode == 'harian') {
                $kategori = $kategori->whereDate('tbl_ikdt.dateupd', $request->waktu)->orderBy('jmlh', 'DESC')->limit(10)->get();
                $title = 'Tanggal ' . date('d F Y', strtotime($request->waktu));
            } else if ($request->priode == 'bulanan') {
                $waktu = explode('-', $request->waktu);
                $kategori = $kategori->whereMonth('tbl_ikdt.dateupd', $waktu[1])->whereYear('tbl_ikdt.dateupd', $waktu[0])->orderBy('jmlh', 'DESC')->limit(10)->get();
                $title = 'Tanggal ' . date('d F Y', strtotime($request->waktu));
            } else if ($request->priode == 'tahunan') {
                $kategori = $kategori->whereYear('tbl_ikdt.dateupd', $request->waktu)->orderBy('jmlh', 'DESC')->limit(10)->get();
                $title = 'Tahun ' . $request->waktu;
            }

            foreach ($kategori as $i => $dta) {
                $nama = Kategori::where('jenis', $dta->jenis)->first()->jenis;
                $data[$i]['nama'] = $nama;
                $data[$i]['jumlah'] = round($dta->jmlh);
                $data[$i]['total'] = 'Rp.' . number_format(round($dta->total));
            }

            $result = [];
            $color = [];
            $label = [];
            $series = [];
            $data_fix = [];
            for ($i = 0; $i < 10; $i++) {
                if (count($data) > 0) {
                    $rand = str_pad(dechex(rand(0x000000, 0xffffff)), 6, 0, STR_PAD_LEFT);
                    $color[] = "#" . $rand;
                    $label[] = $data[$i]['nama'];
                    $series[] = $data[$i]['jumlah'];
                    $data_fix[] = $data[$i];
                }
            }

            $result = [
                "title" => $title,
                "color" => $color,
                "label" => $label,
                "series" => $series,
                "data" => $data_fix,
            ];

            return response()->json($result, 200);
        } else if ($request->req == 'getDetailBrMasuk') {
            $result = BarangMasuk::select('notransaksi', 'tanggal', 'kodesupel', 'kodekantor', 'totalitem', 'subtotal')->where('notransaksi', $request->id)->first();

            $result['tanggal'] = date('d/m/Y H:i', strtotime($result->tanggal));
            $result['kantor'] = ($result->kodekantor == 'GDN') ? 'GUDANG' : 'UTAMA';
            $result['nama_supplier'] = $result->supplier ? $result->supplier->nama : '-';
            $result['jum_barang'] = count($result->detail_barang) . ' Item';
            $result['totalitem'] = round($result->totalitem) . ' Item';
            $result['subtotal'] = 'Rp.' . number_format(round($result->subtotal));

            $dt_barang = [];
            foreach ($result->detail_barang as $dta) {
                $brg = Barang::select('namaitem', 'jenis')->where('kodeitem', $dta->kodeitem)->first();
                $dt_barang[] = [
                    "kodeitem" => $dta->kodeitem,
                    "namaitem" => $brg->namaitem,
                    "jenis" => $brg->jenis,
                    "jumlah" => round($dta->jumlah),
                    "satuan" => $dta->satuan,
                    "harga" => 'Rp.' . number_format(round($dta->harga)),
                    "total" => 'Rp.' . number_format(round($dta->total))
                ];
            }
            $result['dt_barang'] = $dt_barang;

            return response()->json($result, 200);
        } else if ($request->req == 'getDetailBrKeluar') {
            $result = BarangKeluar::select('notransaksi', 'tanggal', 'tipe', 'totalitem', 'totalakhir', 'jmltunai', 'user1')->where('notransaksi', $request->id)->first();

            $result['tanggal'] = date('d/m/Y H:i', strtotime($result->tanggal));
            $result['jum_barang'] = count($result->detail_barang) . ' Item';
            $result['totalitem'] = round($result->totalitem) . ' Item';
            $result['totalakhir'] = 'Rp.' . number_format(round($result->totalakhir));
            $result['jmltunai'] = 'Rp.' . number_format(round($result->jmltunai));

            $dt_barang = [];
            foreach ($result->detail_barang as $dta) {
                $brg = Barang::select('namaitem', 'jenis')->where('kodeitem', $dta->kodeitem)->first();
                $dt_barang[] = [
                    "kodeitem" => $dta->kodeitem,
                    "namaitem" => $brg->namaitem,
                    "jenis" => $brg->jenis,
                    "jumlah" => round($dta->jumlah),
                    "satuan" => $dta->satuan,
                    "harga" => 'Rp.' . number_format(round($dta->harga)),
                    "total" => 'Rp.' . number_format(round($dta->total))
                ];
            }
            $result['dt_barang'] = $dt_barang;

            return response()->json($result, 200);
        } else if ($request->req == 'getTitle') {
            if ($request->priode == 'harian') {
                $title = 'Tanggal ' . date('d F Y', strtotime($request->waktu));
            } else if ($request->priode == 'bulanan') {
                $waktu = explode('-', $request->waktu);
                $title = 'Bulan ' . date('F', strtotime($request->waktu . '-1')) . ' ' . $waktu[0];
            } else if ($request->priode == 'tahunan') {
                $title = 'Tahun ' . $request->waktu;
            }

            $lokasi = '';
            if ($request->lokasi == 'GDN') $lokasi = '(Gudang)';
            else if ($request->lokasi == 'UTM') $lokasi = '(Utama)';

            if ($request->modul) {
                if ($request->modul == 'BLI') $title = 'Pembelian per ' . $title;
                else if ($request->modul == 'JUA') $title = 'Penjualan per ' . $title;
                else if ($request->modul == 'PRS') $title = 'Persediaan per ' . $title;
            }

            return $title . ' ' . $lokasi;
        }
    }
}
