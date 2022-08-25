<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="{{ asset('assets/') }}" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard Analisis Forecasting | Macca Mart</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/icons/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables/datatables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.min.css') }}" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
    <style>
        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            padding: 2px;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="container">
        <div class="card mt-5">
            <div class="card-body">
                <form action="" method="GET">
                    <div class="row">
                        <div class="col-3">
                            <label for="">Priode</label>
                            <input type="month" class="form-control"
                                value="{{ request()->get('priode') ? request()->get('priode') : date('Y-m') }}"
                                name="priode" required>
                        </div>
                        <div class="col-3">
                            <label for="">Kode Barang</label>
                            <input type="text" class="form-control" placeholder="Kode Barang.."
                                value="{{ request()->get('kode') ? request()->get('kode') : '' }}" name="kode"
                                required autocomplete="off">
                        </div>
                        <div class="col-3 pt-4">
                            <button type="submit" class="btn btn-primary">Proses</button>
                        </div>
                        <div class="col-12">
                            <h4 class="mt-3">Hasil Peramalan</h4>
                            @php
                                $barang = DB::table('tbl_item')
                                    ->where('kodeitem', request()->get('kode'))
                                    ->select('namaitem')
                                    ->first();
                            @endphp
                            <b>Nama Barang : {{ $barang ? $barang->namaitem : '-' }}</b>
                        </div>
                        <div class="col-2">
                            <table class="table table-bordered p-1" style="font-size: 11px; font-family:'Calibri';">
                                <thead>
                                    <tr>
                                        <th>Y</th>
                                        <th>YN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (request()->get('kode'))
                                        @php
                                            $kode = request()->get('kode');
                                            $date_now = request()->get('priode') . '-01';
                                            
                                            $x = [];
                                            $y = [];
                                            $x2 = [];
                                            $xy = [];
                                            $data = [];
                                            $date = $date_now;
                                            for ($i = 1; $i <= 60; $i++) {
                                                $date = date('Y-m-d', strtotime('+' . $i - 1 . ' days', strtotime($date_now)));
                                            
                                                $get_jumlah = DB::table('tbl_ikdt')
                                                    ->join('tbl_ikhd', 'tbl_ikdt.notransaksi', '=', 'tbl_ikhd.notransaksi')
                                                    ->select('tbl_ikdt.jumlah')
                                                    ->where('tbl_ikhd.tipe', 'KSR')
                                                    ->where('tbl_ikdt.kodeitem', $kode)
                                                    ->whereDate('tbl_ikhd.tanggal', $date)
                                                    ->get();
                                            
                                                $X = $i;
                                                $Y = 0;
                                                foreach ($get_jumlah as $dta) {
                                                    $Y += round($dta->jumlah);
                                                }
                                            
                                                if ($i <= 30) {
                                                    $data[$i]['y'] = $Y;
                                                } else {
                                                    $data[$i - 30]['yn'] = $Y;
                                                }
                                            
                                                // echo $Y;
                                                // echo '<br>';
                                            
                                                $x[] = $X;
                                                $y[] = $Y;
                                                $x2[] = pow($X, 2);
                                                $xy[] = $X * $Y;
                                            }
                                            // dd($data);
                                        @endphp
                                        @foreach ($data as $dta)
                                            <tr>
                                                <td>{{ $dta['y'] }}</td>
                                                <td>{{ $dta['yn'] }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">
                                                <samll><i>Input Data</i></samll>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/chartjs/chartjs.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/toastr/toastr.min.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    <script src="{{ asset('assets/js/ui-popover.js') }}"></script>

    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>
