@extends('admin.layout')
@section('content')
    @php
    $date = '2022-06-04';
    $data1 = DB::table('tbl_ikdt')->select('total')->whereDate('dateupd', $date)->sum('total');
    $data2 = DB::table('tbl_imdt')->select('total')->whereDate('dateupd', $date)->sum('total');
    $data3 = DB::table('tbl_ikdt')->select('jumlah')->whereDate('dateupd', $date)->sum('jumlah');
    $data4 = DB::table('tbl_itemstok')->select('stok')->sum('stok');

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
                                        class="fw-bold">{{ date('d M Y H:i') }}</span> Harap lakukan sinkronisasi secara
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
                                    <h5 class="m-0 me-2">Order Statistics</h5>
                                    <small class="text-muted">42.82k Total Sales</small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                        <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Share</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <h2 class="mb-2">8,258</h2>
                                        <span>Total Orders</span>
                                    </div>
                                    <div id="orderStatisticsChart"></div>
                                </div>
                                <ul class="p-0 m-0">
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-mobile-alt"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Electronic</h6>
                                                <small class="text-muted">Mobile, Earbuds, TV</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">82.5k</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-success"><i
                                                    class="bx bx-closet"></i></span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Fashion</h6>
                                                <small class="text-muted">T-shirt, Jeans, Shoes</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">23.8k</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-info"><i
                                                    class="bx bx-home-alt"></i></span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Decor</h6>
                                                <small class="text-muted">Fine Art, Dining</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">849k</small>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-secondary">
                                                <i class="bx bx-football"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Sports</h6>
                                                <small class="text-muted">Football, Cricket Kit</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">99</small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--/ Order Statistics -->

                    <!-- Transactions -->
                    <div class="col-md-6 order-2 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title m-0 me-2">Transactions</h5>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                        <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                        <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="p-0 m-0">
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="User"
                                                class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small class="text-muted d-block mb-1">Paypal</small>
                                                <h6 class="mb-0">Send money</h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">+82.6</h6>
                                                <span class="text-muted">USD</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('assets/img/icons/unicons/wallet.png') }}" alt="User"
                                                class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small class="text-muted d-block mb-1">Wallet</small>
                                                <h6 class="mb-0">Mac'D</h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">+270.69</h6>
                                                <span class="text-muted">USD</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('assets/img/icons/unicons/chart.png') }}" alt="User"
                                                class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small class="text-muted d-block mb-1">Transfer</small>
                                                <h6 class="mb-0">Refund</h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">+637.91</h6>
                                                <span class="text-muted">USD</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('assets/img/icons/unicons/cc-success.png') }}"
                                                alt="User" class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small class="text-muted d-block mb-1">Credit Card</small>
                                                <h6 class="mb-0">Ordered Food</h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">-838.71</h6>
                                                <span class="text-muted">USD</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('assets/img/icons/unicons/wallet.png') }}" alt="User"
                                                class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small class="text-muted d-block mb-1">Wallet</small>
                                                <h6 class="mb-0">Starbucks</h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">+203.33</h6>
                                                <span class="text-muted">USD</span>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <img src="{{ asset('assets/img/icons/unicons/cc-warning.png') }}"
                                                alt="User" class="rounded" />
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <small class="text-muted d-block mb-1">Mastercard</small>
                                                <h6 class="mb-0">Ordered Food</h6>
                                            </div>
                                            <div class="user-progress d-flex align-items-center gap-1">
                                                <h6 class="mb-0">-92.45</h6>
                                                <span class="text-muted">USD</span>
                                            </div>
                                        </div>
                                    </li>
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
                                <div class="d-flex justify-content-between flex-sm-row flex-column gap-3 pb-5">
                                    <div
                                        class="d-flex flex-sm-column flex-row align-items-start justify-content-between pb-5">
                                        <div class="card-title">
                                            <h5 class="text-nowrap mt-2 mb-3">Pendapatan</h5>
                                            <span class="badge bg-label-warning rounded-pill">Tahun
                                                {{ date('Y') }}</span>
                                        </div>
                                        <div class="mt-sm-auto">
                                            <small class="text-success text-nowrap fw-semibold"><i
                                                    class="bx bx-chevron-up"></i> 68.2%</small>
                                            <h3 class="mb-0">Rp.84,686,000</h3>
                                        </div>
                                    </div>
                                    <div id="profileReportChart"></div>
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
        });
    </script>
@endsection
