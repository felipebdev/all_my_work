@extends('templates.horizontal.main')

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Galeria</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('gallery.index') }}">Álbum</a></li>
                <li class="breadcrumb-item active">Novo</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
			<h4 class="card-title">Novo álbum</h4>                    
       		@include('gallery._form')
        </div>
    </div>

@endsection