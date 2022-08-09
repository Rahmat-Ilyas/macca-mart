@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Barang();
    $barang = $data->get();
    $date = '2022-06-04';
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forecating /</span> Data Forecating</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Data Forecating</h5>
            <hr>
            <div class="card-datatable table-responsive px-4 pb-4">
                <div class="row justify-content-between">
                    <div class="col-sm-6">
                        <label for="kodebarang"><b>Pilih Barang Yang Ingin Diproses</b></label>
                        <select name="kodebarang" id="kodebarang" class="select2 form-select">
                            <option value="">-- Temukan Kode / Nama Barang --</option>
                            @foreach ($barang as $brg)
                                <option value="{{ $brg->kodeitem }}">{{ $brg->kodeitem . ' / ' . $brg->namaitem }}</option>
                            @endforeach
                        </select>
                        <small class="text-info">* Silahkan pilih barang yang akan di proses (forecasting)</small>
                    </div>
                    <div class="col-sm-4">
                        <div class="alert alert-danger alert-dismissible mt-2 py-2 mb-4" role="alert" id="info-err" hidden="">
                            <small>
                                <strong>Analisis Error:</strong>
                                <ul style="padding-left: 1rem" class="mb-0">
                                    <li>Mean Absolute Error (MAE): <b id="er_mad">0.00</b></li>
                                    <li>Mean Square Error (MSE): <b id="er_mse">0.00</b></li>
                                    <li>Standard Error (SE): <b id="er_se">0.00</b></li>
                                </ul>
                            </small>
                            <button type="button" class="btn-close"></button>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between mt-2">
                    <div class="col-7">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Tanggal Sekarang</b>
                                <span>{{ date('d F Y', strtotime($date)) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Kode Barang</b>
                                <span id="fr_kodebarang"><i>-Pilih barang terlebih dahulu-</i></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Nama Barang</b>
                                <span id="fr_namabarang"><i>-Pilih barang terlebih dahulu-</i></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Stok Barang</b>
                                <span id="fr_stok"><i>-Pilih barang terlebih dahulu-</i></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Minimal Stok</b>
                                <span id="fr_stokmin"><i>-Pilih barang terlebih dahulu-</i></span>
                            </li>
                        </ul>

                        <hr>

                        <h4 class="mb-2">Data Forecasting Penjualan Barang</h4>
                        <table class="table table-bordered dataBarang" style="font-size: 12px;">
                            <thead class="table-secondary">
                                <tr>
                                    <th width="10">No</th>
                                    <th width="150">Tggl Selanjutnya</th>
                                    <th>Perkiraan Terjual</th>
                                    <th>Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 10; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td class="">
                                            {{ date('d F Y', strtotime('+' . $i . ' days', strtotime($date))) }}</td>
                                        <td class="text-center">--</td>
                                        <td class="text-center">--</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>

                    </div>
                    <div class="col-4">
                        <h5 class="fw-bold mb-3"><b>Rekomendasi Waktu Pemesanan Barang Selanjutnya:</b></h5>
                        <div class="alert alert-dark text-center px-1" role="alert">
                            <h1 class="fw-bold mt-2 text-dark date_next">------</h1>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <label for="kodebarang"><b>Perkiraan Ketersediaan Stok Untuk Priode?</b></label>
                            <select name="priode" id="priode" class="form-select">
                                <option value="7">1 Minggu Berikutnya</option>
                                <option value="14">2 Minggu Berikutnya</option>
                                <option value="21">3 Minggu Berikutnya</option>
                                <option value="30">1 Bulan Berikutnya</option>
                                <option value="60">2 Bulan Berikutnya</option>
                            </select>
                        </div>
                        <h5 class="fw-bold mb-4 mt-2"><b>Rekomendasi Stok Pemesanan Barang Selanjutnya:</b></h5>
                        <div class="alert alert-dark text-center" role="alert">
                            <h1 class="fw-bold mt-2 text-dark" id="order_next">------</h1>
                        </div>
                        <div class="mt-0">
                            <small id="info-fr" hidden="">
                                Rekomendasi jumlah stok barang yang harus di pesan pada tanggal <b
                                    class="date_next">{{ date('d F Y') }}</b> untuk persediaan stok sampai tanggal <b
                                    id="priode_date">{{ date('d F Y', strtotime('+7 days', strtotime($date))) }}</b> (<span
                                    id="priode_ket">Priode 1 Minggu Berikutnya</span>)
                            </small>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- <div class="row justify-content-between">
                    <div class="col-7">
                        <h4 class="mb-2">Data Forecasting Penjualan Barang</h4>
                        <table class="table table-bordered dataBarang" style="font-size: 12px;">
                            <thead class="table-secondary">
                                <tr>
                                    <th width="10">No</th>
                                    <th width="150">Tggl Selanjutnya</th>
                                    <th>Perkiraan Terjual</th>
                                    <th>Sisa Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 10; $i++)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td class="">
                                            {{ date('d F Y', strtotime('+' . $i . ' days', strtotime($date))) }}</td>
                                        <td class="text-center">--</td>
                                        <td class="text-center">--</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    <div class="col-4">
                        <h4></h4>
                        <div class="mb-4">
                            <label for="kodebarang"><b>Perkiraan Ketersediaan Stok Untuk Priode?</b></label>
                            <select name="priode" id="priode" class="form-select">
                                <option value="7">1 Minggu Berikutnya</option>
                                <option value="14">2 Minggu Berikutnya</option>
                                <option value="21">3 Minggu Berikutnya</option>
                                <option value="30">1 Bulan Berikutnya</option>
                                <option value="60">2 Bulan Berikutnya</option>
                            </select>
                        </div>
                        <h5 class="fw-bold mb-4 mt-2"><b>Rekomendasi Stok Pemesanan Barang Selanjutnya:</b></h5>
                        <div class="alert alert-dark text-center" role="alert">
                            <h1 class="fw-bold mt-2 text-dark" id="order_next">------</h1>
                        </div>
                        <div class="mt-0">
                            <small id="info-fr" hidden="">
                                Rekomendasi jumlah stok barang yang harus di pesan pada tanggal <b
                                    class="date_next">{{ date('d F Y') }}</b> untuk persediaan stok sampai tanggal <b
                                    id="priode_date">{{ date('d F Y', strtotime('+7 days', strtotime($date))) }}</b> (<span
                                    id="priode_ket">Priode 1 Minggu Berikutnya</span>)
                            </small>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#data-forecasting').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var dataTable = $('.dataBarang').DataTable({
                bLengthChange: false,
                searching: false,
                // bInfo: false,
                // bPaginate: false,
                processing: true,
                serverSide: false,
                aaSorting: []
            });

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

            $('#kodebarang').change(function(e) {
                e.preventDefault();

                var kode = $(this).val();
                var priode = $('#priode').val();
                getData(kode, priode);
            });

            $('#priode').change(function(e) {
                e.preventDefault();

                var kode = $('#kodebarang').val();
                var priode = $(this).val();
                getData(kode, priode);
            });

            $('.btn-close').click(function (e) { 
                e.preventDefault();
                $('#info-err').attr('hidden', '');
            });

            @if (request()->get('kode'))
                var kode = '{{ request()->get('kode') }}';
                $('#kodebarang').val(kode).trigger('change');
                $('#priode').val(30);
                getData(kode, 30);
            @endif

            function getData(kode, priode) {
                $.ajax({
                    url: url,
                    method: "POST",
                    async: true,
                    cache: false,
                    headers: headers,
                    data: {
                        req: 'getForecastingItem',
                        kode: kode,
                        priode: priode,
                    },
                    success: function(res) {
                        console.log(res);
                        if (res) {
                            $.each(res, function(key, val) {
                                $('#' + key).text(val);
                                $('.' + key).text(val);
                            });
                            dataTable.clear().draw();
                            var no = 1;
                            $.each(res.data_fr, function(key, val) {
                                dataTable.row.add([
                                    no,
                                    val.tggl,
                                    val.fr,
                                    val.stok,
                                ]).draw(false);
                                no++;
                            });
                            $('#info-fr').removeAttr('hidden');
                            $('#info-err').removeAttr('hidden');
                        } else {
                            dataTable.clear().draw();
                            @for ($i = 1; $i <= 10; $i++)
                                dataTable.row.add([
                                    '{{ $i }}',
                                    '{{ date('d F Y', strtotime('+' . $i . ' days', strtotime($date))) }}',
                                    '--',
                                    '--',
                                ]).draw(false);
                            @endfor
                            $('#info-fr').attr('hidden', '');
                            $('#info-err').attr('hidden', '');
                            $('#kodebarang').val(null);
                            $("#kodebarang").select2({
                                placeholder: "-- Temukan Kode / Nama Barang --"
                            });
                        }
                    }
                });
            }
        });
    </script>
@endsection
