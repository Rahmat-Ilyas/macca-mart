@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Barang();
    $barang = $data->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Analisis Penjualan /</span> Grafik Penjualan</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Grafik Penjualan</h5>
            <hr>
            <div class="card-datatable table-responsive px-4 pb-4">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="barang">Lihat Berdasarkan Item</label>                        
                        <select name="barang" id="barang" class="select2 form-select">
                            <option value="ALL">LIHAT SEMUA ITEM</option>
                            @foreach ($barang as $brg)
                                <option value="{{ $brg->kodeitem }}">{{ $brg->namaitem }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label for="priode">Lihat Berdasarkan Priode</label>
                        <select name="priode" id="priode" class="form-select">
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label for="waktu">Pilih Priode Waktu</label>
                        <input type="week" id="waktu" class="form-control"
                            value="{{ date('Y') . '-W' . date('W') }}"">
                    </div>
                </div>
                <hr>
                <div>
                    <div class="card">
                        <div class="card-header header-elements p-3 my-n1">
                            <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2">Grafik Penjualan per <span id="lab-waktu"
                                    style="text-transform: capitalize;">Minggu {{ date('W, Y') }}</span></h5>
                        </div>
                        <div class="card-body">
                            <canvas id="barChart" class="chartjs" height="400"></canvas>
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
            $('#grafik-penjualan').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

            $('#priode').change(function(e) {
                e.preventDefault();

                var priode = $(this).val();
                var barang = $('#barang').val();
                var waktu;
                if (priode == 'mingguan') {
                    waktu = "{{ date('Y') . '-W' . date('W') }}";
                    $('#waktu').attr('type', 'week').val(waktu);
                } else if (priode == 'bulanan') {
                    waktu = "{{ date('Y-m') }}";
                    $('#waktu').attr('type', 'month').val(waktu);
                } else {
                    waktu = "{{ date('Y') }}"
                    $('#waktu').attr('type', 'number').val(waktu);
                }

                getData(barang, priode, waktu);
            });

            $(document).on('change keyup', '#waktu', function(e) {
                e.preventDefault();
                var waktu = $(this).val();
                var priode = $('#priode').val();
                var barang = $('#barang').val();

                getData(barang, priode, waktu);
            });

            $('#barang').change(function(e) {
                e.preventDefault();

                var barang = $(this).val();
                var priode = $('#priode').val();
                var waktu = $('#waktu').val();

                getData(barang, priode, waktu);
            });

            getData('ALL', 'mingguan', "{{ date('Y') . '-W' . date('W') }}");

            function getData(barang, priode, waktu) {
                const barChart = document.getElementById('barChart');
                $.ajax({
                    url: url,
                    method: "POST",
                    async: true,
                    headers: headers,
                    data: {
                        req: 'getGrafik',
                        priode: priode,
                        waktu: waktu,
                        barang: barang,
                    },
                    success: function(data) {
                        $('#lab-waktu').text(data.title);
                        // Color Variables
                        const cyanColor = '#28dac6';
                        let borderColor, gridColor, tickColor;
                        borderColor = '#f0f0f0';
                        gridColor = '#f0f0f0';
                        tickColor = 'rgba(0, 0, 0, 0.75)';

                        var chartExist = Chart.getChart("barChart");
                        if (chartExist != undefined)
                            chartExist.destroy();

                        const barChartVar = new Chart(barChart, {
                            type: 'bar',
                            data: {
                                labels: data.label,
                                datasets: [{
                                    data: data.data,
                                    backgroundColor: cyanColor,
                                    borderColor: 'transparent',
                                    maxBarThickness: 15,
                                    borderRadius: {
                                        topRight: 15,
                                        topLeft: 15
                                    }
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: {
                                    duration: 500
                                },
                                plugins: {
                                    tooltip: {
                                        rtl: false,
                                        backgroundColor: config.colors.white,
                                        titleColor: config.colors.black,
                                        bodyColor: config.colors.black,
                                        borderWidth: 1,
                                        borderColor: borderColor
                                    },
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            color: gridColor,
                                            borderColor: borderColor
                                        },
                                        ticks: {
                                            color: tickColor
                                        }
                                    },
                                    y: {
                                        grid: {
                                            color: gridColor,
                                            borderColor: borderColor
                                        },
                                        ticks: {
                                            stepSize: 100,
                                            tickColor: gridColor,
                                            color: tickColor
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection
