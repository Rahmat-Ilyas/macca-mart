@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Analisis Penjualan /</span> Kategori Paling Laku</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Kategori Paling Laku</h5>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <div class="card-datatable table-responsive px-4 pb-4">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="kategori">Lihat Berdasarkan Priode</label>
                                <select name="kategori" id="priode" class="form-select">
                                    <option value="harian">Harian</option>
                                    <option value="bulanan">Bulanan</option>
                                    <option value="tahunan">Tahunan</option>
                                </select>
                            </div>

                            <div class="col-sm-6">
                                <label for="kategori">Pilih Priode Waktu</label>
                                <input type="date" id="waktu" class="form-control" value="{{ date('Y-m-d') }}"">
                            </div>
                        </div>

                        <hr>
                        <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2 mb-2">Top 10 Kategori Paling Laku per <span
                                id="lab-waktu">Tanggal {{ date('d F Y') }}</span></h5>
                        <table class="table table-bordered dataBarang" style="font-size: 11px;">
                            <thead>
                                <tr>
                                    {{-- <th>Kode Barang</th> --}}
                                    <th>Nama Kategori</th>
                                    <th>Total Terjual</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4 class="text-center mt-4">Kategori Paling Laku</h4>
                    <div id="donutChart" class="mb-5"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#kategori-paling-laku').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var dataTable = $('.dataBarang').DataTable({
                bLengthChange: false,
                searching: false,
                bInfo: false,
                bPaginate: false,
                processing: true,
                serverSide: false,
                aaSorting: []
            });

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

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

            const donutChartEl = document.querySelector('#donutChart');
            const donutChartConfig = {
                chart: {
                    height: 550,
                    type: 'donut'
                },
                labels: ['Belum Ada Data'],
                series: [0.01],
                colors: ['#28dac6'],
                stroke: {
                    show: false,
                    curve: 'straight'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opt) {
                        return parseInt(val) + '%';
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom'
                },
                plotOptions: {
                    pie: {
                        donut: {
                            labels: {
                                show: true,
                                name: {
                                    fontSize: '1rem',
                                    fontFamily: 'Public Sans'
                                },
                                value: {
                                    fontSize: '1rem',
                                    fontFamily: 'Public Sans',
                                    formatter: function(val) {
                                        return parseInt(val) + '%';
                                    }
                                },
                                total: {
                                    show: true,
                                    fontSize: '1.5rem',
                                    label: 'Total Item',
                                    formatter: function(val) {
                                        var count = 0;
                                        for (var i = 0; i < val.globals.series.length; ++i) {
                                            count += val.globals.series[i];
                                        }
                                        return parseInt(count) + ' Item';
                                    }
                                }
                            }
                        }
                    }
                }
            };

            const donutChart = new ApexCharts(donutChartEl, donutChartConfig);
            donutChart.render();

            function getData(priode, waktu) {
                $.ajax({
                    url: url,
                    method: "POST",
                    async: true,
                    cache: false,
                    headers: headers,
                    data: {
                        req: 'getKategoriLaku',
                        priode: priode,
                        waktu: waktu,
                    },
                    success: function(res) {
                        $('#lab-waktu').text(res.title);
                        if (res.data.length > 0) {
                            dataTable.clear().draw();
                            $.each(res.data, function(key, val) {
                                dataTable.row.add([
                                    // val.kodeitem,
                                    val.nama,
                                    val.jumlah + ' Item',
                                    val.total,
                                ]).draw(false);
                            });
                            donutChart.updateOptions({
                                labels: res.label,
                                series: res.series,
                                colors: res.color,
                            });
                        } else {
                            dataTable.clear().draw();
                            donutChart.updateOptions({
                                labels: ['Belum Ada Data'],
                                series: [0.01],
                                colors: ['#28dac6'],
                            });
                        }
                    }
                });
            }
        });
    </script>
@endsection
