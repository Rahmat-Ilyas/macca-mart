@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Data Barang /</span> Barang Masuk</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Data Barang Masuk</h5>
            <hr>
            <div class="card-datatable table-responsive px-4 pb-4">
                <div class="row">
                    <div class="col-sm-2">
                        <label for="lokasi">Lokasi</label>
                        <select id="lokasi" class="form-select">
                            <option value="ALL">Semua</option>
                            <option value="GDN">GUDANG</option>
                            <option value="UTM">UTAMA</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label for="kategori">Lihat Berdasarkan Priode</label>
                        <select name="kategori" id="priode" class="form-select">
                            <option value="harian">Harian</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan" selected="">Tahunan</option>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label for="kategori">Pilih Priode Waktu</label>
                        <input type="text" id="waktu" class="form-control" value="{{ date('Y') }}"">
                    </div>
                </div>

                <hr>
                <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2 mb-2">Data Barang Masuk per <span
                                id="lab-waktu">Tahun {{ date('Y') }}</span></h5>
                <table class="table table-striped dataBarang" style="font-size: 12px;">
                    <thead>
                        <tr>
                            {{-- <th width="10">No</th> --}}
                            <th>Nomor Transaksi</th>
                            <th>Tggl Masuk</th>
                            <th>Kantor</th>
                            <th>Nama Supplier</th>
                            <th>Jumlah Item</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Extra Large Modal -->
    <div class="modal fade modal-detail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel4">Detail Pembelian Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td width="200"><b>Nomor Transaksi</b></td>
                                    <td width="10">:</td>
                                    <td>Msjdhuueee</td>
                                </tr>
                                <tr>
                                    <td><b>Tanggal Masuk</b></td>
                                    <td>:</td>
                                    <td>14/02/2022</td>
                                </tr>
                                <tr>
                                    <td><b>Kantor</b></td>
                                    <td>:</td>
                                    <td>Utama</td>
                                </tr>
                                <tr>
                                    <td><b>Supplier</b></td>
                                    <td>:</td>
                                    <td>Utama</td>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#barang-masuk').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

            $(document).on('click', '.btn-detail', function() {
                var id = $(this).attr('data-id');
                $.ajax({
                    url: url,
                    method: "POST",
                    async: true,
                    cache: false,
                    headers: headers,
                    data: {
                        req: 'getDetailBrMasuk',
                        id: id
                    },
                    success: function(res) {
                        $('#lab-waktu').text(res);
                    }
                });
            });

            $('#priode').change(function(e) {
                e.preventDefault();

                var priode = $(this).val();
                var lokasi = $('#lokasi').val();
                var waktu;
                if (priode == 'harian') {
                    waktu = "{{ date('Y-m-d') }}";
                    $('#waktu').attr('type', 'date').val(waktu);
                } else if (priode == 'bulanan') {
                    waktu = "{{ date('Y-m') }}";
                    $('#waktu').attr('type', 'month').val(waktu);
                } else {
                    waktu = "{{ date('Y') }}"
                    $('#waktu').attr('type', 'number').val(waktu);
                }
                
                getData(lokasi, priode, waktu);
            });
            
            $(document).on('change keyup', '#waktu', function(e) {
                e.preventDefault();
                var waktu = $(this).val();
                var priode = $('#priode').val();
                var lokasi = $('#lokasi').val();

                getData(lokasi, priode, waktu);
            });

            $('#lokasi').change(function(e) {
                e.preventDefault();

                var lokasi = $(this).val();
                var priode = $('#priode').val();
                var waktu = $('#waktu').val();
                
                getData(lokasi, priode, waktu);
            });

            getData('ALL', 'tahunan', "{{ date('Y') }}");

            function getData(lokasi, priode, waktu) {
                getTitle(lokasi, priode, waktu);
                $(".dataBarang").dataTable().fnDestroy();
                $('.dataBarang').DataTable({
                    pageLength: 50,
                    bLengthChange: true,
                    bFilter: true,
                    bInfo: true,
                    bAutoWidth: true,
                    searching: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: url + '/datatable',
                        method: "POST",
                        headers: headers,
                        data: {
                            req: 'getBarangMasuk',
                            lokasi: lokasi,
                            priode: priode,
                            waktu: waktu,
                        },
                        async: true,
                        error: function(res) {},
                        complete: function(res) {
                            $('[data-toggle1="tooltip"]').tooltip();
                        }
                    },
                    deferRender: true,
                    responsive: !0,
                    colReorder: !0,
                    pagingType: "full_numbers",
                    stateSave: !1,
                    language: {
                        zeroRecords: "Belum ada data...",
                        processing: 'Mengambil Data...',
                    },
                    columns: [
                        // {
                        //     data: 'no',
                        //     render: function(data, type, row, meta) {
                        //         return meta.row + 1;
                        //     }
                        // },
                        {
                            data: 'notransaksi',
                            name: 'notransaksi',
                        },
                        {
                            data: 'tanggal',
                            name: 'tanggal'
                        },
                        {
                            data: 'kodekantor',
                            name: 'kodekantor'
                        },
                        {
                            data: 'supplier',
                            name: 'supplier'
                        },
                        {
                            data: 'jum_barang',
                            name: 'jum_barang'
                        },
                        {
                            data: 'totalitem',
                            name: 'totalitem'
                        },
                        {
                            data: 'subtotal',
                            name: 'subtotal'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }

            function getTitle(lokasi, priode, waktu) {
                $.ajax({
                    url: url,
                    method: "POST",
                    async: true,
                    cache: false,
                    headers: headers,
                    data: {
                        req: 'getTitle',
                        priode: priode,
                        waktu: waktu,
                        lokasi: lokasi
                    },
                    success: function(res) {
                        $('#lab-waktu').text(res);
                    }
                });
            }
        });
    </script>
@endsection
