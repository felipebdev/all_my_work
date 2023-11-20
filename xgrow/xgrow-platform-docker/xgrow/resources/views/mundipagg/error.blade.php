@extends('mundipagg.header')

@section('content')
    <div id="model_right" class="container-fluid fill">
        <div class="row fill  d-flex justify-content-center align-items-center">
            @if ($errors->any())
                <div class="alert alert-warning">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
@endsection
