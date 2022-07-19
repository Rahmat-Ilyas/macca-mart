@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Supplier /</span> Perbandingan Supplier</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Perbandingan Supplier</h5>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <div class="card-datatable table-responsive px-4 pb-4">
                        <table class="table table-striped dataBarang" style="font-size: 12px;">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th width="210">Nama Supplier</th>
                                    <th>Item Masuk</th>
                                    <th>Item Terjual</th>
                                    <th>Presentase</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-4">
                    <h4 class="text-center mt-4">Grafik Presentase Penjualan</h4>
                    <div id="donutChart"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#perbandingan-supplier').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

            getData();

            function getData() {
                $(".dataBarang").dataTable().fnDestroy();
                $('.dataBarang').DataTable({
                    bLengthChange: true,
                    bFilter: true,
                    bInfo: true,
                    bAutoWidth: true,
                    // searching: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: url + '/datatable',
                        method: "POST",
                        headers: headers,
                        data: {
                            req: 'getPrbnSupplier'
                        },
                        async: true,
                        error: function(res) {},
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
                    columns: [{
                            data: 'kode',
                            name: 'kode',
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'item_masuk',
                            name: 'item_masuk'
                        },
                        {
                            data: 'kota',
                            name: 'kota'
                        },
                        {
                            data: 'provinsi',
                            name: 'provinsi'
                        },
                    ]
                });
            }
        });


        const donutChartEl = document.querySelector('#donutChart');
        const donutChartConfig = {
            chart: {
                height: 390,
                type: 'donut'
            },
            labels: ['Operational', 'Networking', 'Hiring', 'R&D'],
            series: [85, 15, 50, 50],
            colors: ['#fee802', '#2b9bf4', '#826bf8', '#3fd0bd'],
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
                                fontSize: '2rem',
                                fontFamily: 'Open Sans'
                            },
                            value: {
                                fontSize: '1rem',
                                fontFamily: 'Open Sans',
                                formatter: function(val) {
                                    return parseInt(val) + '%';
                                }
                            },
                            total: {
                                show: true,
                                fontSize: '1.5rem',
                                label: 'Operational',
                                formatter: function(w) {
                                    return '31%';
                                }
                            }
                        }
                    }
                }
            },
            responsive: [{
                    breakpoint: 992,
                    options: {
                        chart: {
                            height: 380
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                },
                {
                    breakpoint: 576,
                    options: {
                        chart: {
                            height: 320
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true,
                                        name: {
                                            fontSize: '1.5rem'
                                        },
                                        value: {
                                            fontSize: '1rem'
                                        },
                                        total: {
                                            fontSize: '1.5rem'
                                        }
                                    }
                                }
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                },
                {
                    breakpoint: 420,
                    options: {
                        legend: {
                            show: false
                        }
                    }
                },
                {
                    breakpoint: 360,
                    options: {
                        chart: {
                            height: 280
                        },
                        legend: {
                            show: false
                        }
                    }
                }
            ]
        };
        if (typeof donutChartEl !== undefined && donutChartEl !== null) {
            const donutChart = new ApexCharts(donutChartEl, donutChartConfig);
            donutChart.render();
        }
    </script>
@endsection
