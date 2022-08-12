@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Data Barang /</span> Kategori Barang</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Kategori Barang</h5>
            <hr>
            <div class="card-datatable table-responsive px-4 pb-4">
                <table class="table table-striped dataBarang" style="font-size: 12px;">
                    <thead>
                        <tr>
                            <th width="10">No</th>
                            <th>Nama Kategori</th>
                            <th>Keterangan</th>
                            <th>Jumlah Barang</th>
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
            $('#kategori-barang').addClass('active').parents('.this-sub').addClass('active open');
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
                    pageLength: 50,
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
                            req: 'getKategori'
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
                    columns: [
                        {
                            data: 'no',
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {
                            data: 'jenis',
                            name: 'jenis',
                        },
                        {
                            data: 'ketjenis',
                            name: 'ketjenis'
                        },
                        {
                            data: 'jumitem',
                            name: 'jumitem'
                        }
                    ]
                });
            }
        });
    </script>
@endsection
