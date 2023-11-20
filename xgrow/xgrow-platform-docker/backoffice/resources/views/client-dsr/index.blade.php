@extends('templates.horizontal.main')

@section('jquery')

@endsection

@push('before-styles')

    <link rel="stylesheet" type="text/css" href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.min.css" integrity="sha512-rxThY3LYIfYsVCWPCW9dB0k+e3RZB39f23ylUYTEuZMDrN/vRqLdaCBo/FbvVT6uC2r0ObfPzotsfKF9Qc5W5g==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}">

@endpush

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')

    <!-- This is data table -->
    <script src="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/datatables.min.js"></script>
    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js" integrity="sha512-mVkLPLQVfOWLRlC2ZJuyX5+0XrTlbW2cyAwyqgPkLGxhoaHNSWesYMlcUjX8X+k45YB8q90s88O7sos86636NQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- add the shim first -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/shim.min.js" integrity="sha512-nPnkC29R0sikt0ieZaAkk28Ib7Y1Dz7IqePgELH30NnSi1DzG4x+envJAOHz8ZSAveLXAHTR3ai2E9DZUsT8pQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- after the shim is referenced, add the library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.1/xlsx.full.min.js" integrity="sha512-MRDODtdVPB+P6eG8RPTGDxaK55jJ8j+Iu2eJFUa+3lmeOLTrXfDbQ4ThAw7vx0iqYlAZodtE4ps23rd/NQHoXg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script type="module" src="{{ asset('/js/client-dsr.js') }}"></script>

    <script>

		window.COLUMNS = <?= json_encode($columns); ?>;
		window.STATUS = <?= json_encode($status); ?>;

    </script>

@endpush

@section('content')

    <div class="row page-titles title-fix">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Relatórios</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/#">Relatórios</a>
                </li>
                <li class="breadcrumb-item active">Requisição DSR / LGPD</li>
            </ol>
        </div>
    </div>
    <div class="card">
        <div class="card-body">

            @include('client-dsr._form-filter')

            <div class="table-responsive m-t-30">
                <table id="report-table" class="table table-bordered">

                    <thead>

                    <tr class="header-info">
                        <th colspan="{{$totalColumns['student']}}" class="student">Aluno</th>
                    </tr>

                    <tr class="header-names">

                        @foreach ($columns as $column)

                            <th data-name="{{$column['name']}}" data-index="{{$loop->index}}" class="{{$column['owner']}} sort-column">

                                <span class="label">{{$column['label']}}</span>
                                <span class="badge badge-warning badge-sort hidden" data-index="{{$loop->index}}"></span>

                            </th>

                        @endforeach

                    </tr>

                    </thead>

                    <tbody>

                    </tbody>

                    <tfoot>

                    <tr class="header-names">

                        @foreach ($columns as $column)

                            <th data-name="{{$column['name']}}" data-index="{{$loop->index}}" class="{{$column['owner']}} sort-column">
                                <span class="label">{{$column['label']}}</span>
                                <span class="badge badge-warning badge-sort hidden" data-index="{{$loop->index}}"></span>

                            </th>

                        @endforeach

                    </tr>

                    <tr class="header-info">
                        <th colspan="{{$totalColumns['student']}}" class="student">Aluno</th>
                    </tr>

                    </tfoot>

                </table>
            </div>
        </div>
    </div>

@endsection
