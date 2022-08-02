@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Analisis /</span> Keuangan</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Keuangan</h5>
            <hr>
            <div class="card-datatable table-responsive px-4 pb-4">
                <div class="row">
                    <div class="col-sm-2">
                        <label for="modul">Modul</label>
                        <select id="modul" class="form-select">
                            <option value="BLI">Pembelian</option>
                            <option value="JUA">Penjualan</option>
                            <option value="PRS">Persediaan</option>
                        </select>
                    </div>

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
                <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2 mb-2">Daftar Jurnal <span id="lab-waktu">Pembelian per Tanggal
                        {{ date('Y-m-d') }}</span></h5>
                <table class="table table-striped dataBarang" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th width="130">Nomor Transaksi</th>
                            <th>Tanggal</th>
                            <th width="100">Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Keterangan</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#keuangan').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

            $('#priode').change(function(e) {
                e.preventDefault();

                var priode = $(this).val();
                var waktu;
                var modul = $('#modul').val();
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

                getData(modul, priode, waktu);
            });

            $(document).on('change keyup', '#waktu', function(e) {
                e.preventDefault();
                var waktu = $(this).val();
                var priode = $('#priode').val();
                var modul = $('#modul').val();

                getData(modul, priode, waktu);
            });

            $('#modul').change(function(e) {
                e.preventDefault();

                var modul = $(this).val();
                var priode = $('#priode').val();
                var waktu = $('#waktu').val();

                getData(modul, priode, waktu);
            });

            getData('BLI', 'harian', "{{ date('Y-m-d') }}");

            function getData(modul, priode, waktu) {
                getTitle(modul, priode, waktu);
                $(".dataBarang").dataTable().fnDestroy();
                $('.dataBarang').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ordering: false,
                    searching: false,
                    aaSorting: [],
                    ajax: {
                        url: url + '/datatable',
                        method: "POST",
                        headers: headers,
                        dataType: "json",
                        data: {
                            req: 'getJurnal',
                            priode: priode,
                            waktu: waktu,
                            modul: modul
                        },
                        async: true,
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
                            data: 'kodeacc',
                            name: 'kodeacc'
                        },
                        {
                            data: 'nama_akun',
                            name: 'nama_akun'
                        },
                        {
                            data: 'keterangan',
                            name: 'keterangan'
                        },
                        {
                            data: 'debet',
                            name: 'debet'
                        },
                        {
                            data: 'kredit',
                            name: 'kredit'
                        }
                    ]
                });
            }

            function getTitle(modul, priode, waktu) {
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
                        modul: modul
                    },
                    success: function(res) {
                        $('#lab-waktu').text(res);
                    }
                });
            }
        });
    </script>
@endsection

