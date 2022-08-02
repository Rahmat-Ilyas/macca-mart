@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Forecating /</span> Forecating Barang</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Forecating Barang</h5>
            <hr>
            <div class="card-datatable table-responsive px-4 pb-4">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="kategori">Kategori</label>
                        <select name="kategori" id="kategori" class="select2 form-select">
                            <option value="SEMUA">SEMUA KATEGORI</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->jenis }}">{{ $kat->jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr>
                <table class="table table-striped dataBarang" style="font-size: 12px;">
                    <thead>
                        <tr>
                            {{-- <th width="10">No</th> --}}
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Stok (GDN/UTM)</th>
                            <th>Min Stok</th>
                            <th>Stok</th>
                            <th>Perkiraan Pesanan Selanjutnya</th>
                            <th>Perkiraan Jumlah Pesanan</th>
                            {{-- <th>Action</th> --}}
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
            $('#forecasting-barang').addClass('active').parents('.this-sub').addClass('active open');
            $(".select2").select2();

            var url = "{{ url('admin/config') }}";
            var headers = {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }

            getData('SEMUA');

            $('#kategori').change(function(e) {
                e.preventDefault();

                var jenis = $(this).val();
                getData(jenis);
            });

            function getData(jenis) {
                $(".dataBarang").dataTable().fnDestroy();
                $('.dataBarang').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    aaSorting: [],
                    ajax: {
                        url: url + '/datatable',
                        method: "POST",
                        headers: headers,
                        data: {
                            req: 'getFrBarang',
                            jenis: jenis
                        },
                        async: true,
                        error: function(res) {},
                    },
                    language: {
                        zeroRecords: "Belum ada data...",
                        processing: 'Mengambil Data...',
                    },
                    columns: [
                        {
                            data: 'kodeitem',
                            name: 'kodeitem',
                        },
                        {
                            data: 'namaitem',
                            name: 'namaitem'
                        },
                        {
                            data: 'stok_gu',
                            name: 'stok_gu'
                        },
                        {
                            data: 'stokmin',
                            name: 'stokmin'
                        },
                        {
                            data: 'total_stok',
                            name: 'total_stok'
                        },
                        {
                            data: 'hargapokok',
                            name: 'hargapokok'
                        },
                        {
                            data: 'hargajual1',
                            name: 'hargajual1'
                        },
                        // {
                        //     data: 'action',
                        //     name: 'action',
                        //     orderable: false,
                        //     searchable: false
                        // },
                    ]
                });
            }
        });
    </script>
@endsection
