@extends('templates.xgrow.main')

@push('before-styles')
    <style>
        .custom-link {
            color: #92bc1d;
        }

        .custom-link:hover {
            color: #ffffff;
        }
    </style>
@endpush

@push('after-scripts')
<script>
    const modelLink = @json(asset('/xgrow-vendor/assets/files/ModeloArquivoEnvioCupons.csv'));
    const plansB = @json($plans);

    const plans = plansB.map((item) => {
        return {
            value: item.id,
            name: item.name
        }
    });
</script>
<script src="{{ asset('js/bundle/import-subscribers.js') }}"></script>
@endpush

@section('content')
    <div id="importSubscribersPage">
        <router-view></router-view>
    </div>
@endsection
