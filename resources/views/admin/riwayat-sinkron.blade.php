@extends('admin.layout')
@section('content')
    @php
    $data = new App\Models\Kategori();
    $kategori = $data->orderBy('jenis', 'asc')->get();
    $date = '2022-06-04';
    @endphp
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Riwayat Sinkron</h4>

        <!-- Basic Bootstrap Table -->
        <div class="card">
            <h5 class="card-header pb-3">Riwayat Sinkron</h5>
            <hr>
            <div class="row">
                <div class="col-8">
                    <div class="card-datatable table-responsive px-4 pb-4">
                        <table class="table table-bordered dataBarang" style="font-size: 12px;">
                            <thead class="table-secondary">
                                <tr>
                                    <th width="10">No</th>
                                    <th>Tanggal Sinkron</th>
                                    <th>Jam</th>
                                    <th>Total Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{ date('d F Y', strtotime('2022-05-25')) }}</td>
                                    <td>{{ date('H:i', strtotime('2022-05-25 14:22')) }}</td>
                                    <td>1500 Data</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>{{ date('d F Y', strtotime('2022-05-30')) }}</td>
                                    <td>{{ date('H:i', strtotime('2022-05-30 10:30')) }}</td>
                                    <td>980 Data</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>{{ date('d F Y', strtotime('2022-06-02')) }}</td>
                                    <td>{{ date('H:i', strtotime('2022-06-02 12:12')) }}</td>
                                    <td>402 Data</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>{{ date('d F Y', strtotime('2022-06-04')) }}</td>
                                    <td>{{ date('H:i', strtotime('2022-06-04 07:00')) }}</td>
                                    <td>200 Data</td>
                                </tr>
                            </tbody>
                        </table>
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
            $('#riwayat-sinkron').addClass('active').parents('.this-sub').addClass('active open');

            var dataTable = $('.dataBarang').DataTable();
        });
    </script>
@endsection
