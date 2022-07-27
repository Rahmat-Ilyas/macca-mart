@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
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
                    <div class="col-sm-3">
                        <label for="kategori">Lihat Berdasarkan Priode</label>
                        <select name="kategori" id="priode" class="form-select">
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan">Bulanan</option>
                            <option value="tahunan">Tahunan</option>
                        </select>
                    </div>

                    <div class="col-sm-3">
                        <label for="kategori">Pilih Priode Waktu</label>
                        <input type="week" id="waktu" class="form-control" value="{{ date('Y').'-W'.date('W') }}"">
                    </div>
                </div>
                <hr>
                <div>
                    <div class="card">
                        <div class="card-header header-elements p-3 my-n1">
                            <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2">Grafik Penjualan per <span id="lab-waktu" style="text-transform: capitalize;">Minggu {{ date('W, Y') }}</span></h5>
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
                var waktu;
                if (priode == 'mingguan') {
                    waktu = "{{ date('Y').'-W'.date('W') }}";
                    $('#waktu').attr('type', 'week').val(waktu);
                } else if (priode == 'bulanan') {
                    waktu = "{{ date('Y-m') }}";
                    $('#waktu').attr('type', 'month').val(waktu);
                } else {
                    waktu = "{{ date('Y') }}"
                    $('#waktu').attr('type', 'number').val(waktu);
                }

                getData(priode, waktu);
            });
            
            $('#waktu').change(function (e) { 
                e.preventDefault();
                var waktu = $(this).val();
                var priode = $('#priode').val();
                
                getData(priode, waktu);
            });

            getData('mingguan', "{{ date('Y').'-W'.date('W') }}");

            function getData(priode, waktu) {
                var strp = priode.substr(0, priode.length-2);
                $('#lab-waktu').text(strp+' '+waktu)

                $.ajax({
                    url     : url,
                    method  : "POST",
                    headers : headers,
                    data 	: { 
                        req: 'getGrafik',
                        priode: priode,
                        waktu: waktu,
                    },
                    success : function(data) {
                    }
                });

                // Color Variables
                const cyanColor = '#28dac6';
                let borderColor, gridColor, tickColor;
                borderColor = '#f0f0f0';
                gridColor = '#f0f0f0';
                tickColor = 'rgba(0, 0, 0, 0.75)';

                const barChart = document.getElementById('barChart');
                const barChartVar = new Chart(barChart, {
                    type: 'bar',
                    data: {
                        labels: [
                            '1 Jan',
                            '2 Jan',
                            '3 Jan',
                            '4 Jan',
                            '5 Jan',
                            '6 Jan',
                            '7 Jan',
                            '8 Jan',
                            '9 Jan',
                            '10 Jan',
                            '11 Jan',
                            '12 Jan',
                            '13 Jan',
                            '14 Jan',
                            '15 Jan',
                            '16 Jan',
                            '17 Jan',
                            '18 Jan',
                            '19 Jan',
                            '20 Jan',
                            '21 Jan',
                            '22 Jan',
                            '23 Jan',
                            '24 Jan',
                            '25 Jan',
                            '26 Jan',
                            '27 Jan',
                            '28 Jan',
                            '29 Jan',
                            '30 Jan',
                        ],
                        datasets: [{
                            data: [275, 90, 190, 205, 125, 85, 55, 87, 127, 150, 230, 280, 190, 10, 10,
                                10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10
                            ],
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
    </script>
@endsection
