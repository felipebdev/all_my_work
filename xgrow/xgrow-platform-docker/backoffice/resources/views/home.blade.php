@extends('templates.horizontal.main')

@section('jquery') {{-- Including this section to override it empty. Using jQuery from webpack build --}} @endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@section('content')

    <div class="card">
        <div class="card-body">

            <h4 class="font-weight-bold py-3 mb-4">Home</h4>

            <p>This page is an example of basic layout to get you started.</p>

        </div>
    </div>

@endsection
