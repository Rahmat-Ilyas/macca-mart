<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

use App\Models\Admin;
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

    public  function update(Request $request, $target)
    {
        if ($target == 'akun') {
            $akun = Admin::where('id', $request->id)->first();

            if ($request->password == '') $except = ['_token', 'id', 'password'];
            else {
                $except = ['_token', 'id'];
                $request['password'] = bcrypt($request->password);
            }
            foreach ($request->except($except) as $key => $data) {
                $akun->$key = $data;
            }
            $akun->save();

            return back()->with('success', 'Data akun berhasil diupdate');
        }
    }

    public  function datatable(Request $request)
    {
        if ($request->req == 'getBarang') {
            $result = DB::table('tbl_item')->join('tbl_itemstok', 'tbl_itemstok.kodeitem', '=', 'tbl_item.kodeitem')->selectRaw('tbl_item.*, SUM(tbl_itemstok.stok) as total_stok')->groupBy('tbl_item.kodeitem')->orderBy('total_stok', 'DESC');
            if ($request->jenis != 'SEMUA') {
                $result = $result->where('jenis', $request->jenis);
            }

            return DataTables::of($result)->addColumn('no', function ($dta) {
                return null;
            })->addColumn('hargapokok', function ($dta) {
                return number_format($dta->hargapokok, 2, ',', '.');
            })->addColumn('hargajual1', function ($dta) {
                return number_format($dta->hargajual1, 2, ',', '.');
            })->addColumn('stok_gu', function ($dta) {
                $gdn = DB::table('tbl_itemstok')->where('kodeitem', $dta->kodeitem)->where('kantor', 'GDN')->first();
                $gdn = $gdn ? round($gdn->stok) : '0';
                $utm = DB::table('tbl_itemstok')->where('kodeitem', $dta->kodeitem)->where('kantor', 'UTM')->first();
                $utm = $utm ? round($utm->stok) : '0';

                return $gdn . ' / ' . $utm . ' (' . $dta->satuan . ')';
            })->addColumn('total_stok', function ($dta) {
                return round($dta->total_stok) . ' ' . $dta->satuan;
            })->toJson();
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
            })->addColumn('rf_tggl_pesanan', function ($dta) {
                $tggl = $this->forecasting_v1($dta->kodeitem, 0)['date_next'];
                return date('d M Y', strtotime($tggl));
            })->addColumn('fr_stok_pesanan', function ($dta) {
                $item = $this->forecasting_v1($dta->kodeitem, 30)['order_next'];
                return $item . ' ' . $dta->satuan;
            })->addColumn('action', function ($dta) {
                return '<div class="text-center">
				<a href="' . url('admin/forecasting/data-forecasting?kode=' . $dta->kodeitem) . '" role="button" class="btn btn-info btn-sm waves-effect waves-light btn-detail" data-toggle1="tooltip" title="Lihat Selengkapnya"><i class="bx bx-analyse"></i></a>
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

                    if ($request->barang == 'ALL') $getdb = DB::table('tbl_ikdt');
                    else $getdb = DB::table('tbl_ikdt')->where('kodeitem', $request->barang);
                    $getitemklr = $getdb->select('dateupd', 'jumlah')->whereDate('dateupd', $date)->get();
                    $jumlah = 0;
                    foreach ($getitemklr as $jum) {
                        $jumlah += round($jum->jumlah);
                    }
                    $data[] = $jumlah;
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

                    if ($request->barang == 'ALL') $getdb = DB::table('tbl_ikdt');
                    else $getdb = DB::table('tbl_ikdt')->where('kodeitem', $request->barang);
                    $getitemklr = $getdb->select('dateupd', 'jumlah')->whereDate('dateupd', $date)->get();
                    $jumlah = 0;
                    foreach ($getitemklr as $jum) {
                        $jumlah += round($jum->jumlah);
                    }
                    $data[] = $jumlah;
                }
                $title = 'Bulan ' . date('F', strtotime($date)) . ' ' . $waktu[0];
            } else if ($request->priode == 'tahunan') {
                $label = [];
                $data = [];
                for ($i = 1; $i <= 12; $i++) {
                    $date = $request->waktu . '-' . $i . '-1';
                    $label[] = date('F', strtotime($date));

                    if ($request->barang == 'ALL') $getdb = DB::table('tbl_ikdt');
                    else $getdb = DB::table('tbl_ikdt')->where('kodeitem', $request->barang);
                    $getitemklr = $getdb->select('dateupd', 'jumlah')->whereMonth('dateupd', $i)->whereYear('dateupd', $request->waktu)->get();
                    $jumlah = 0;
                    foreach ($getitemklr as $jum) {
                        $jumlah += round($jum->jumlah);
                    }
                    $data[] = $jumlah;
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
        } else if ($request->req == 'getForecastingItem') {
            $gdn = DB::table('tbl_itemstok')->where('kodeitem', $request->kode)->where('kantor', 'GDN')->first();
            $gdn = $gdn ? round($gdn->stok) : 0;
            $utm = DB::table('tbl_itemstok')->where('kodeitem', $request->kode)->where('kantor', 'UTM')->first();
            $utm = $utm ? round($utm->stok) : 0;
            $barang = DB::table('tbl_item')->where('kodeitem', $request->kode)->select('stokmin', 'kodeitem', 'namaitem', 'satuan')->first();

            if ($barang) {
                $stok = ($gdn + $utm) < 0 ? 0 : ($gdn + $utm);
                $stok_brg = $stok;
                $stokmin = $barang ? round($barang->stokmin) : 0;
                $satuan = $barang ? $barang->satuan : 0;

                $date_now = '2022-06-04';
                $date_first = date('Y-m-d', strtotime('-30 days', strtotime($date_now)));

                // get jarak waktu
                $frs = new DateTime($date_first);
                $now = new DateTime($date_now);
                $jarak = $now->diff($frs)->days;

                $x = [];
                $y = [];
                $x2 = [];
                $xy = [];
                $date = $date_now;

                for ($i = 1; $i <= $jarak; $i++) {
                    $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($date_first)));

                    $get_jumlah = DB::table('tbl_ikdt')->join('tbl_ikhd', 'tbl_ikdt.notransaksi', '=', 'tbl_ikhd.notransaksi')->select('tbl_ikdt.jumlah')->where('tbl_ikhd.tipe', 'KSR')->where('tbl_ikdt.kodeitem', $request->kode)->whereDate('tbl_ikhd.tanggal', $date)->get();

                    $X = $i;
                    $Y = 0;
                    foreach ($get_jumlah as $dta) {
                        $Y += round($dta->jumlah);
                    }

                    $x[] = $X;
                    $y[] = $Y;
                    $x2[] = pow($X, 2);
                    $xy[] = $X * $Y;
                }

                $a = ((array_sum($y) * array_sum($x2)) - (array_sum($x) * array_sum($xy))) / ((count($x) * array_sum($x2)) - (pow(array_sum($x), 2)));
                $b = ((count($x) * array_sum($xy)) - (array_sum($x) * array_sum($y))) / ((count($x) * array_sum($x2)) - (pow(array_sum($x), 2)));

                $data_fr = [];
                $date_next = $date_now;
                $x_next = 0;
                $j = 1;
                while ($stokmin <= $stok) {
                    $date_next = date('Y-m-d', strtotime('+' . $j . ' days', strtotime($date)));
                    $x_next = count($x) + $j;
                    $fr = $a + ($b * $x_next);
                    $fr = ($fr <= 0) ? 1 : $fr;
                    $stok = $stok - $fr;
                    $stok_vw = round($stok) < 0 ? 0 : round($stok);

                    $data_fr[] = [
                        "tggl" => date('d F Y', strtotime($date_next)),
                        "fr" => round($fr) . ' ' . $satuan,
                        "stok" => $stok_vw . ' ' . $satuan,
                    ];
                    $j++;
                }

                $priode_date = $date_next;
                $order_next = 0;
                $x_next2 = $x_next;
                for ($l = 1; $l <= $request->priode; $l++) {
                    $priode_date = date('Y-m-d', strtotime('+' . $l . ' days', strtotime($date_next)));
                    $x_next2 = $x_next + $l;
                    $fr = $a + ($b * $x_next2);
                    $fr = ($fr <= 0) ? 1 : $fr;
                    $order_next += number_format($fr);
                }

                // analisis error
                $abs = [];
                $sqr = [];
                foreach ($y as $i => $dt) {
                    $fr = $a + ($b * ($i + 1));
                    $err = $dt - $fr;
                    $abs[] = abs($err);
                    $sqr[] = pow($err, 2);
                }

                $mad = array_sum($abs) / count($abs);
                $mse = array_sum($sqr) / count($sqr);
                $se = sqrt(array_sum($sqr) / count($sqr));

                if ($request->priode == 7) $priode_ket = 'Priode 1 Minggu Berikutnya';
                else if ($request->priode == 14) $priode_ket = 'Priode 2 Minggu Berikutnya';
                else if ($request->priode == 21) $priode_ket = 'Priode 3 Minggu Berikutnya';
                else if ($request->priode == 30) $priode_ket = 'Priode 1 Bulan Berikutnya';
                else if ($request->priode == 60) $priode_ket = 'Priode 2 Bulan Berikutnya';

                $result = [
                    "fr_kodebarang" => $barang->kodeitem,
                    "fr_namabarang" => $barang->namaitem,
                    "fr_stok" => round($stok_brg) . ' ' . $barang->satuan,
                    "fr_stokmin" => round($stokmin) . ' ' . $barang->satuan,
                    "er_mad" => number_format($mad, 2),
                    "er_mse" => number_format($mse, 2),
                    "er_se" => number_format($se, 2),
                    "date_next" => date('d F Y', strtotime($date_next)),
                    "order_next" => $order_next . ' ' . $barang->satuan,
                    "priode_date" => date('d F Y', strtotime($priode_date)),
                    "priode_ket" => $priode_ket,
                    "data_fr" => $data_fr,
                ];
                return response()->json($result, 200);
            } else return null;

        }
    }

    private function forecasting_v1($kodeitem, $waktu)
    {
        $gdn = DB::table('tbl_itemstok')->where('kodeitem', $kodeitem)->where('kantor', 'GDN')->first();
        $gdn = $gdn ? round($gdn->stok) : 0;
        $utm = DB::table('tbl_itemstok')->where('kodeitem', $kodeitem)->where('kantor', 'UTM')->first();
        $utm = $utm ? round($utm->stok) : 0;
        $barang = DB::table('tbl_item')->where('kodeitem', $kodeitem)->select('stokmin')->first();
        $stok = $gdn + $utm;
        $stokmin = round($barang->stokmin);

        $date_now = '2022-06-04';
        $date_first = date('Y-m-d', strtotime('-30 days', strtotime($date_now)));

        // get jarak waktu
        $frs = new DateTime($date_first);
        $now = new DateTime($date_now);
        $jarak = $now->diff($frs)->days;

        $x = [];
        $y = [];
        $x2 = [];
        $xy = [];
        $date = $date_now;

        for ($i = 1; $i <= $jarak; $i++) {
            $date = date('Y-m-d', strtotime('+' . $i . ' days', strtotime($date_first)));

            $get_jumlah = DB::table('tbl_ikdt')->join('tbl_ikhd', 'tbl_ikdt.notransaksi', '=', 'tbl_ikhd.notransaksi')->select('tbl_ikdt.jumlah')->where('tbl_ikhd.tipe', 'KSR')->where('tbl_ikdt.kodeitem', $kodeitem)->whereDate('tbl_ikhd.tanggal', $date)->get();

            $X = $i;
            $Y = 0;
            foreach ($get_jumlah as $dta) {
                $Y += round($dta->jumlah);
            }

            $x[] = $X;
            $y[] = $Y;
            $x2[] = pow($X, 2);
            $xy[] = $X * $Y;
        }

        $a = ((array_sum($y) * array_sum($x2)) - (array_sum($x) * array_sum($xy))) / ((count($x) * array_sum($x2)) - (pow(array_sum($x), 2)));
        $b = ((count($x) * array_sum($xy)) - (array_sum($x) * array_sum($y))) / ((count($x) * array_sum($x2)) - (pow(array_sum($x), 2)));

        $date_next = $date_now;
        $x_next = 0;
        $j = 1;
        while ($stokmin <= $stok) {
            $date_next = date('Y-m-d', strtotime('+' . $j . ' days', strtotime($date)));
            $x_next = count($x) + $j;
            $fr = $a + ($b * $x_next);
            $fr = ($fr <= 0) ? 1 : $fr;
            $stok = $stok - $fr;
            $j++;
        }

        $order_next = 0;
        $x_next2 = $x_next;
        for ($l = 1; $l <= $waktu; $l++) {
            $x_next2 = $x_next + $l;
            $fr = $a + ($b * $x_next2);
            $fr = ($fr <= 0) ? 1 : $fr;
            $order_next += number_format($fr);
        }

        $data = [
            "date_next" => $date_next,
            "order_next" => $order_next
        ];

        return $data;
    }
}
