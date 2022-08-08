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
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->

            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo text-center mt-0">
                    <a href="index.html" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/img/icons/logo.png') }}" height="60" width=80"
                                alt="">
                        </span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                {{-- <div class="menu-inner-shadow"></div> --}}

                <ul class="menu-inner py-1">
                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Start</span>
                    </li>
                    <!-- Dashboard -->
                    <li class="menu-item" id="dashboard">
                        <a href="{{ url('admin/') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-item" id="sinkron">
                        <a href="{{ url('admin/riwayat-sinkron') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-sync"></i>
                            <div>Riwayat Sinkron</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Main Menu</span>
                    </li>

                    <li class="menu-item this-sub">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-analyse"></i>
                            <div>Forecasting</div>
                        </a>

                        <ul class="menu-sub">
                            <li class="menu-item" id="forecasting-barang">
                                <a href="{{ url('admin/forecasting/forecasting-barang') }}" class="menu-link">
                                    <div data-i18n="Without menu">Forecasting Barang</div>
                                </a>
                            </li>
                            <li class="menu-item" id="data-forecasting">
                                <a href="{{ url('admin/forecasting/data-forecasting') }}" class="menu-link">
                                    <div data-i18n="Without navbar">Data Forecasting</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item this-sub">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-box"></i>
                            <div data-i18n="Account Settings">Data Barang</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item" id="data-barang">
                                <a href="{{ url('admin/data-barang/data-barang') }}" class="menu-link">
                                    <div data-i18n="Account">List Data Barang</div>
                                </a>
                            </li>
                            <li class="menu-item" id="barang-masuk">
                                <a href="{{ url('admin/data-barang/barang-masuk') }}" class="menu-link">
                                    <div data-i18n="Notifications">Barang Masuk</div>
                                </a>
                            </li>
                            <li class="menu-item" id="kategori-barang">
                                <a href="{{ url('admin/data-barang/kategori') }}" class="menu-link">
                                    <div data-i18n="Connections">Kategori Barang</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-item this-sub">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-archive-in"></i>
                            <div data-i18n="Misc">Supplier</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item" id="data-supplier">
                                <a href="{{ url('admin/supplier/data-supplier') }}" class="menu-link">
                                    <div data-i18n="Error">Data Supplier</div>
                                </a>
                            </li>
                            <li class="menu-item" id="perbandingan-supplier">
                                <a href="{{ url('admin/supplier/perbandingan-supplier') }}" class="menu-link">
                                    <div data-i18n="Under Maintenance">Perbandingan Supplier</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="menu-header small text-uppercase"><span class="menu-header-text">Analisis</span></li>
                    <li class="menu-item this-sub">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-line-chart"></i>
                            <div data-i18n="Misc">Analisis Penjualan</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item" id="grafik-penjualan">
                                <a href="{{ url('admin/analisis-penjualan/grafik-penjualan') }}" class="menu-link">
                                    <div data-i18n="Error">Grafik Penjualan</div>
                                </a>
                            </li>
                            <li class="menu-item" id="produk-paling-laku">
                                <a href="{{ url('admin/analisis-penjualan/produk-paling-laku') }}" class="menu-link">
                                    <div data-i18n="Under Maintenance">Produk Paling Laku</div>
                                </a>
                            </li>
                            <li class="menu-item" id="kategori-paling-laku">
                                <a href="{{ url('admin/analisis-penjualan/kategori-paling-laku') }}"
                                    class="menu-link">
                                    <div data-i18n="Under Maintenance">Kategori Paling Laku</div>
                                </a>
                            </li>
                            <li class="menu-item" id="produk-kurang-laku">
                                <a href="{{ url('admin/analisis-penjualan/produk-kurang-laku') }}" class="menu-link">
                                    <div data-i18n="Under Maintenance">Produk Kurang Laku</div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="menu-item" id="data-transaksi">
                        <a href="{{ url('admin/data-transaksi') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-barcode"></i>
                            <div data-i18n="Basic">Transaksi Penjualan</div>
                        </a>
                    </li>

                    <li class="menu-item" id="keuangan">
                        <a href="{{ url('admin/keuangan') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-wallet"></i>
                            <div data-i18n="Basic">Keuangan</div>
                        </a>
                    </li>

                    {{-- <li class="menu-header small text-uppercase"><span class="menu-header-text">Laporan</span></li>

                    <li class="menu-item">
                        <a href="cards-basic.html" class="menu-link">
                            <i class="menu-icon tf-icons bx bxs-report"></i>
                            <div data-i18n="Basic">Laporan</div>
                        </a>
                    </li> --}}
                </ul>
            </aside>
            <!-- / Menu -->
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->

                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="bx bx-search fs-4 lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none"
                                    placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->

                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- Place this tag where you want the button to render. -->
                            <li class="nav-item lh-1">
                                <a class="text-dark" href="#">{{ Auth::user()->nama }}</a>
                            </li>

                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('assets/img/avatars/admin.png') }}" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="{{ asset('assets/img/avatars/admin.png') }}" alt
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-semibold d-block">{{ Auth::user()->nama }}</span>
                                                    <small class="text-muted">Admin</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                            data-bs-target=".modal-akun">
                                            <i class="bx bx-user me-2"></i>
                                            <span class="align-middle">Pengaturan Akun</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ url('admin/logout') }}">
                                            <i class="bx bx-power-off me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>

                <!-- / Navbar -->
                <!-- Content wrapper -->
                <div class="content-wrapper">

                    @yield('content')

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div
                            class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                <script>
                                    document.write('©' + new Date().getFullYear());
                                </script> Macca Mart Forecasting by
                                <a href="#" target="_blank" class="footer-link fw-bolder">Doreka Studio</a>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->

            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <div class="modal fade modal-akun" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel4">Pengaturan Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-5">
                    <table class="table table-bordered" id="detail-akun">
                        <tbody>
                            <tr>
                                <td width="150">Nama Admin</td>
                                <td width="10">:</td>
                                <td>{{ Auth::user()->nama }}</td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td>:</td>
                                <td>{{ Auth::user()->username }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <form method="POST" action="{{ url('admin/update/akun') }}" id="edit-akun"
                        hidden="">
                        @csrf
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Nama</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                                <input type="text" name="nama" class="form-control" required=""
                                    autocomplete="off" placeholder="Nama.." value="{{ Auth::user()->nama }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9">
                                <input type="text" name="username" class="form-control" required=""
                                    autocomplete="off" placeholder="Username.."
                                    value="{{ Auth::user()->username }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-sm-3 col-form-label">Password</label>
                            <div class="col-sm-9">
                                <input type="text" name="password" class="form-control" placeholder="Password.."
                                    autocomplete="off">
                                <span class="text-info">Note: Biarkan kosong jika tidak diganti</span>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-9">
                                <button type="button" class="btn btn-secondary" id="btn-batal-edit">Batal</button>
                                <button type="submit" class="btn btn-primary text-white">Simpan</button>
                            </div>
                        </div>
                    </form>
                    <div class="text-center" id="akun-kontrol">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" id="btn-edit-akun">Edit Akun</button>
                    </div>
                </div>
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

    @yield('javascript')

    <script>
        $(document).ready(function() {
            $('[data-toggle1="tooltip"]').tooltip()
            $('.datatable').DataTable();

            $('#btn-edit-akun').click(function(event) {
                $('#edit-akun').removeAttr('hidden');
                $('#detail-akun').attr('hidden', '');
                $('#akun-kontrol').attr('hidden', '');
            });

            $('#btn-batal-edit').click(function(event) {
                $('#edit-akun').attr('hidden', '');
                $('#detail-akun').removeAttr('hidden');
                $('#akun-kontrol').removeAttr('hidden');
            });

            @if (session('success'))
                toastr.success('{{ session('success') }}.', "Berhasil");
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error('{{ $error }}.', "Gagal");
                @endforeach
            @endif
        });
    </script>

</body>

</html>
