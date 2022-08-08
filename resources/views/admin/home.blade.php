@extends('admin.layout')
@section('content')
    @php
    $date = '2022-06-04 12:45';
    $data1 = DB::table('tbl_ikdt')
        ->select('total')
        ->whereDate('dateupd', $date)
        ->sum('total');
    $data2 = DB::table('tbl_imdt')
        ->select('total')
        ->whereDate('dateupd', $date)
        ->sum('total');
    $data3 = DB::table('tbl_ikdt')
        ->select('jumlah')
        ->whereDate('dateupd', $date)
        ->sum('jumlah');
    $data4 = DB::table('tbl_itemstok')
        ->select('stok')
        ->sum('stok');

    $gdt = [];
    $glb = [];
    $gdtm = [];
    $gdtk = [];
    for ($i = 1; $i <= 6; $i++) {
        $bln = $i;
        if (date('m', strtotime($date)) > 6) {
            $bln = $i + 6;
        }

        $dta1 = DB::table('tbl_ikdt')
            ->select('total')
            ->whereMonth('dateupd', $bln)
            ->whereYear('dateupd', date('Y'))
            ->sum('total');

        $dta2 = DB::table('tbl_imdt')
            ->select('total')
            ->whereMonth('dateupd', $bln)
            ->whereYear('dateupd', date('Y'))
            ->sum('total');

        $gdtm[] = round($dta1);
        $gdtk[] = round($dta2);

        $prft = round($dta1) - round($dta2);
        $gdt[] = $prft;
        $glb[] = "'" . date('M', strtotime(date('Y-' . $bln . '-d'))) . "'";
    }
    $data5 = implode(', ', $gdt);
    $label1 = implode(', ', $glb);

    $data6 = array_sum($gdtm);
    $data7 = array_sum($gdtk);

    // Page 2
    $kategori = DB::table('tbl_ikdt')
        ->join('tbl_item', 'tbl_ikdt.kodeitem', '=', 'tbl_item.kodeitem')
        ->selectRaw('tbl_item.jenis, SUM(tbl_ikdt.jumlah) as jmlh, SUM(tbl_ikdt.total) as total')
        ->groupBy('tbl_item.jenis')
        ->whereDate('tbl_ikdt.dateupd', $date)
        ->orderBy('jmlh', 'DESC')
        ->limit(4)
        ->get();

    $gdt = [];
    $glb = [];
    foreach ($kategori as $i => $dta) {
        $nama = DB::table('tbl_itemjenis')
            ->where('jenis', $dta->jenis)
            ->first()->jenis;
        $gdt[] = round($dta->jmlh);
        $glb[] = "'" . $nama . "'";
    }

    $data8 = implode(', ', $gdt);
    $label2 = implode(', ', $glb);

    $transaksi = DB::table('tbl_ikhd')
        ->whereDate('dateupd', $date)
        ->where('tipe', 'KSR')
        ->orderBy('dateupd', 'DESC')
        ->limit(6)
        ->get();

    $colors = ['primary', 'warning', 'info', 'secondary', 'danger', 'success'];
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-8 mb-4 order-0">
                <div class="card">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-8">
                            <div class="card-body">
                                <h5 class="card-title text-primary">Selamat Datang {{ Auth::user()->nama }}! 🎉</h5>
                                <p class="mb-4">
                                    Sinkronisasi Database terakhir dilakukan pada <span
                                        class="fw-bold">{{ date('d M Y H:i', strtotime($date)) }}</span> Harap lakukan
                                    sinkronisasi secara
                                    berkala!
                                </p>

                                <a href="{{ url('admin/riwayat-sinkron') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="menu-icon tf-icons bx bx-sync"></i> Lihat Riwayat Sinkron
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-4 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                    alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png') }}"
                                    data-app-light-img="illustrations/man-with-laptop-light.png') }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 order-1">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="bx bx-wallet"></i>
                                        </span>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Pendapatan</span>
                                <h3 class="card-title mb-2">{{ number_format($data1) }}</h3>
                                <small class="text-success fw-semibold">{{ date('d M Y', strtotime($date)) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-danger">
                                            <i class='bx bx-credit-card'></i>
                                        </span>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Pengeluaran</span>
                                <h3 class="card-title text-nowrap mb-1">{{ number_format($data2) }}</h3>
                                <small class="text-danger fw-semibold">{{ date('d M Y', strtotime($date)) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Revenue -->
            <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
                <div class="row">
                    <!-- Order Statistics -->
                    <div class="col-md-6 order-0 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between pb-0">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2">Statik Penjualan Per Kategori</h5>
                                    <small class="text-muted">{{ date('d F Y', strtotime($date)) }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                        <a class="dropdown-item"
                                            href="{{ url('admin/analisis-penjualan/kategori-paling-laku') }}">Lihat
                                            Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <h2 class="mb-2">{{ number_format($data3) }}</h2>
                                        <span>Total Item Terjual</span>
                                    </div>
                                    <div id="statikPenjualan"></div>
                                </div>
                                <ul class="p-0 m-0">
                                    @foreach ($kategori as $i => $dta)
                                        @php
                                            $kat = DB::table('tbl_itemjenis')
                                                ->where('jenis', $dta->jenis)
                                                ->first();
                                        @endphp
                                        <li class="d-flex mb-4 pb-1">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-{{ $colors[$i] }}">
                                                    @if ($i % 2 == 1)
                                                        <i class='bx bxs-category'></i>
                                                    @else
                                                        <i class='bx bxs-category-alt'></i>
                                                    @endif
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-0">{{ ucwords(strtolower($kat->jenis)) }}</h6>
                                                    <small class="text-muted">{{ $kat->ketjenis }}</small>
                                                </div>
                                                <div class="user-progress">
                                                    <small class="fw-semibold">{{ round($dta->jmlh) }} Item</small>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--/ Order Statistics -->

                    <!-- Transactions -->
                    <div class="col-md-6 order-2 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="card-title mb-0">
                                    <h5 class="m-0 me-2">Transaksi Penjualan Terbaru</h5>
                                    <small class="text-muted">{{ date('d F Y', strtotime($date)) }}</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                        <a class="dropdown-item" href="{{ url('admin/data-transaksi') }}">Lihat
                                            Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    @foreach ($transaksi as $i => $dta)
                                        @php
                                            $getitem = DB::table('tbl_ikdt')
                                                ->join('tbl_item', 'tbl_ikdt.kodeitem', '=', 'tbl_item.kodeitem')
                                                ->select('tbl_item.namaitem')
                                                ->where('notransaksi', $dta->notransaksi);
                                            $jumitem = count($getitem->get());
                                            $item = substr($getitem->first()->namaitem, 0, 17);
                                            $item = ucwords(strtolower($item));
                                            if ($jumitem > 1) {
                                                $jum = $jumitem-1;
                                                $item = $item.', <b>'.$jum.'+ item</b>';
                                            }
                                        @endphp
                                        <li class="d-flex mb-4 pb-1">
                                            <div class="avatar flex-shrink-0 me-3">
                                                <span class="avatar-initial rounded bg-label-success">
                                                    <i class='bx bx-credit-card'></i>
                                                </span>
                                            </div>
                                            <div
                                                class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="me-2">
                                                    <h6 class="mb-1">#{{ $dta->notransaksi }}</h6>
                                                    <small class="text-muted d-block mb-0">{!! $item !!}</small>
                                                </div>
                                                <div class="user-progress d-flex align-items-center gap-1">
                                                    <span class="text-muted">+ RP</span>
                                                    <h6 class="mb-0">{{ number_format($dta->totalakhir) }}</h6>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--/ Transactions -->
                </div>
            </div>
            <!--/ Total Revenue -->
            <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
                <div class="row">
                    <div class="col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class='bx bx-cart'></i>
                                        </span>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Total Penjualan</span>
                                <h3 class="card-title text-nowrap mb-1">{{ number_format($data3) }} Item</h3>
                                <small class="text-info fw-semibold">{{ date('d M Y', strtotime($date)) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class='bx bxs-package'></i>
                                        </span>
                                    </div>
                                </div>
                                <span class="fw-semibold d-block mb-1">Total Stok</span>
                                <h3 class="card-title mb-2">{{ number_format($data4) }}</h3>
                                <small class="text-primary fw-semibold">{{ date('d M Y', strtotime($date)) }}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                    <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                        <div class="card-title">
                                            <h5 class="text-nowrap mt-2 mb-3">Profile Report</h5>
                                            <span class="badge bg-label-warning rounded-pill">Tahun
                                                {{ date('Y') }}</span>
                                        </div>
                                    </div>
                                    <div id="totalPendapatan"></div>
                                </div>
                                <div class="mt-sm-auto mb-3">
                                    <small class="text-success text-nowrap fw-semibold"><i
                                            class="bx bx-chevron-up"></i>Pendapatan</small>
                                    <h3 class="mb-0">Rp{{ number_format($data6) }}</h3>
                                </div>
                                <div class="mt-sm-auto mb-4">
                                    <small class="text-danger text-nowrap fw-semibold"><i
                                            class="bx bx-chevron-down"></i>Pengeluaran</small>
                                    <h3 class="mb-0">Rp{{ number_format($data7) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#dashboard').addClass('active');

            let cardColor, headingColor, axisColor, shadeColor, borderColor;

            cardColor = config.colors.white;
            headingColor = config.colors.headingColor;
            axisColor = config.colors.axisColor;
            borderColor = config.colors.borderColor;

            const totalPendapatanEl = document.querySelector('#totalPendapatan'),
                totalPendapatanConfig = {
                    chart: {
                        height: 80,
                        // width: 175,
                        type: 'line',
                        toolbar: {
                            show: false
                        },
                        dropShadow: {
                            enabled: true,
                            top: 10,
                            left: 5,
                            blur: 3,
                            color: config.colors.warning,
                            opacity: 0.15
                        },
                        sparkline: {
                            enabled: true
                        }
                    },
                    grid: {
                        show: false,
                        padding: {
                            right: 8
                        }
                    },
                    colors: [config.colors.warning],
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 4,
                        curve: 'smooth'
                    },
                    series: [{
                        data: [{{ $data5 }}]
                    }],
                    labels: [{!! $label1 !!}],
                    xaxis: {
                        show: false,
                        lines: {
                            show: false
                        },
                        labels: {
                            show: false
                        },
                        axisBorder: {
                            show: false
                        }
                    },
                    yaxis: {
                        show: false
                    }
                };
            if (typeof totalPendapatanEl !== undefined && totalPendapatanEl !== null) {
                const totalPendapatan = new ApexCharts(totalPendapatanEl, totalPendapatanConfig);
                totalPendapatan.render();
            }


            // Order Statistics Chart
            // --------------------------------------------------------------------
            const chartOrderStatistics = document.querySelector('#statikPenjualan'),
                orderChartConfig = {
                    chart: {
                        height: 175,
                        width: 140,
                        type: 'donut'
                    },
                    labels: [{!! $label2 !!}],
                    series: [{{ $data8 }}],
                    colors: [config.colors.primary, config.colors.warning, config.colors.info, config.colors
                        .secondary
                    ],
                    stroke: {
                        width: 5,
                        colors: cardColor
                    },
                    dataLabels: {
                        enabled: false,
                        formatter: function(val, opt) {
                            return parseInt(val);
                        }
                    },
                    legend: {
                        show: false
                    },
                    grid: {
                        padding: {
                            top: 0,
                            bottom: 0,
                            right: 15
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '75%',
                                labels: {
                                    show: true,
                                    value: {
                                        fontSize: '1.5rem',
                                        fontFamily: 'Public Sans',
                                        color: headingColor,
                                        offsetY: -15,
                                        formatter: function(val) {
                                            return parseInt(val);
                                        }
                                    },
                                    name: {
                                        offsetY: 20,
                                        fontFamily: 'Public Sans'
                                    },
                                    total: {
                                        show: true,
                                        fontSize: '0.8125rem',
                                        color: config.colors.primary,
                                        label: '{{ str_replace("'", '', explode(', ', $label2)[0]) }}',
                                        formatter: function(w) {
                                            return '{{ explode(', ', $data8)[0] }}';
                                        }
                                    }
                                }
                            }
                        }
                    }
                };
            if (typeof chartOrderStatistics !== undefined && chartOrderStatistics !== null) {
                const statisticsChart = new ApexCharts(chartOrderStatistics, orderChartConfig);
                statisticsChart.render();
            }
        });
    </script>
@endsection
