@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Analisis /</span> Data Transaksi</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Data Transaksi</h5>
            <hr>
            <div class="card-datatable table-responsive px-4 pb-4">
                <div class="row">
                    <div class="col-sm-3">
                        <label for="kategori">Lihat Berdasarkan Priode</label>
                        <select name="kategori" id="priode" class="form-select">
                            <option value="harian">Harian</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label for="kategori">Pilih Priode Waktu</label>
                        <input type="date" id="waktu" class="form-control" value="{{ date('Y-m-d') }}"">
                    </div>
                </div>

                <hr>
                <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2 mb-2">Data Transaksi per <span id="lab-waktu">Tanggal
                        {{ date('Y-m-d') }}</span></h5>
                <table class="table table-striped dataBarang" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th>Nomor Transaksi</th>
                            <th>Tggl Transaksi</th>
                            <th>Jumlah Item</th>
                            <th>Total Item</th>
                            <th>Total Harga</th>
                            <th>Jumlah Bayar</th>
                            <th>Kasir</th>
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
                        <div class="col-12">
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td width="200"><b>Nomor Transaksi</b></td>
                                        <td width="10">:</td>
                                        <td id="notransaksi">-</td>

                                        <td></td>

                                        <td width="200"><b>Total Harga</b></td>
                                        <td width="10">:</td>
                                        <td id="totalakhir">-</td>
                                    </tr>
                                    <tr>
                                        <td><b>Tanggal Transaksi</b></td>
                                        <td>:</td>
                                        <td id="tanggal">-</td>

                                        <td></td>

                                        <td><b>Jumlah Bayar</b></td>
                                        <td>:</td>
                                        <td id="jmltunai">-</td>
                                    </tr>
                                    <tr>
                                        <td><b>Jumlah Item</b></td>
                                        <td>:</td>
                                        <td id="jum_barang">-</td>

                                        <td></td>

                                        <td><b>Kasir</b></td>
                                        <td>:</td>
                                        <td id="user1">-</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Item</b></td>
                                        <td>:</td>
                                        <td id="totalitem">-</td>

                                        <td></td>

                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-12">
                            <h4 class="text-center mt-3">List Barang Terjual</h4>
                            <hr>
                            <table class="table table-striped detailBarang" style="font-size: 11px;">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Jmlh Beli</th>
                                        <th>Satuan</th>
                                        <th>Hrg Satuan</th>
                                        <th>Total Hrg</th>
                                    </tr>
                                </thead>
                                <tbody>

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
            $('#data-transaksi').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

            var detailBarang = $('.detailBarang').DataTable({
                bLengthChange: false,
                searching: false,
                bInfo: false,
                aaSorting: []
            });
            $(document).on('click', '.btn-detail', function() {
                var id = $(this).attr('data-id');
                $.ajax({
                    url: url,
                    method: "POST",
                    async: true,
                    cache: false,
                    headers: headers,
                    data: {
                        req: 'getDetailBrKeluar',
                        id: id
                    },
                    success: function(res) {
                        console.log(res);
                        $.each(res, function(key, val) {
                            $('#' + key).text(val);
                        });

                        detailBarang.clear().draw();
                        $.each(res.dt_barang, function(key, val) {
                            detailBarang.row.add([
                                val.kodeitem,
                                val.namaitem,
                                val.jenis,
                                val.jumlah,
                                val.satuan,
                                val.harga,
                                val.total
                            ]).draw(false);
                        });
                    }
                });
            });

            $('#priode').change(function(e) {
                e.preventDefault();

                var priode = $(this).val();
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

                getData(priode, waktu);
            });

            $(document).on('change keyup', '#waktu', function(e) {
                e.preventDefault();
                var waktu = $(this).val();
                var priode = $('#priode').val();

                getData(priode, waktu);
            });

            getData('harian', "{{ date('Y-m-d') }}");

            function getData(priode, waktu) {
                getTitle(priode, waktu);
                $(".dataBarang").dataTable().fnDestroy();
                $('.dataBarang').DataTable({
                    // pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: url + '/datatable',
                        method: "POST",
                        headers: headers,
                        data: {
                            req: 'getDataTransaksi',
                            priode: priode,
                            waktu: waktu,
                        },
                        async: true,
                        complete: function(res) {
                            $('[data-toggle1="tooltip"]').tooltip();
                        }
                    },
                    language: {
                        zeroRecords: "Belum ada data...",
                        processing: 'Mengambil Data...',
                    },
                    columns: [
                        {
                            data: 'notransaksi',
                            name: 'notransaksi',
                        },
                        {
                            data: 'tanggal',
                            name: 'tanggal'
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
                            data: 'totalakhir',
                            name: 'totalakhir'
                        },
                        {
                            data: 'jmltunai',
                            name: 'jmltunai'
                        },
                        {
                            data: 'user1',
                            name: 'user1'
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

            function getTitle(priode, waktu) {
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
                    },
                    success: function(res) {
                        $('#lab-waktu').text(res);
                    }
                });
            }
        });
    </script>
@endsection
