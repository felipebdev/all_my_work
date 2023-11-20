@extends('templates.monster.main')

@section('jquery')

@endsection

@push('before-styles')

    <link rel="stylesheet" type="text/css"
          href="/vendor/wrappixel/monster-admin/4.2.1/assets/plugins/datatables/media/css/dataTables.bootstrap4.css">

@endpush

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')

@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">E-mails</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">Configurações</li>
                <li class="breadcrumb-item active">E-mails</li>
            </ol>
        </div>
    </div>

@endsection
