@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Analisis Penjualan /</span> Produk Kurang Laku</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Produk Kurang Laku</h5>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <div class="card-datatable table-responsive px-4 pb-4">
                        <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2 mb-2">List 500 Produk Kurang Laku</h5>
                        <table class="table table-bordered dataBarang" style="font-size: 11px;">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Total Penjualan</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    {{-- <h4 class="text-center mt-4">Produk Kurang Laku</h4>
                    <div id="donutChart" class="mb-5"></div> --}}
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#produk-kurang-laku').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var dataTable = $('.dataBarang').DataTable({
                // bLengthChange: false,
                // searching: false,
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

            getData();

            const donutChartEl = document.querySelector('#donutChart');
            const donutChartConfig = {
                chart: {
                    height: 1000,
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

            function getData() {
                $.ajax({
                    url: url,
                    method: "POST",
                    async: true,
                    cache: false,
                    headers: headers,
                    data: {
                        req: 'getProdukKrLaku',
                    },
                    success: function(res) {
                        $('#lab-waktu').text(res.title);
                        if (res.data.length > 0) {
                            dataTable.clear().draw();
                            $.each(res.data, function(key, val) {
                                dataTable.row.add([
                                    val.kodeitem,
                                    val.nama,
                                    val.jumlah + ' Item',
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
