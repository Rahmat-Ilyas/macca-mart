@extends('admin.layout')
@section('content')
    @php
        $data = new App\Models\Sinkron();
        $sinkron = $data->orderBy('id', 'desc')->get();
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
                                @foreach ($sinkron as $i => $dta)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ date('d/m/Y', strtotime($dta->created_at)) }}</td>
                                        <td>{{ date('H:i', strtotime($dta->created_at)) }}</td>
                                        <td>{{ $dta->jumlah_data }} Data</td>
                                    </tr>
                                @endforeach
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
