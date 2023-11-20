@extends('templates.horizontal.main')

@section('jquery')
    <script>
        $(document).ready(function (){
            $('#message').summernote({
                height: 300, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                focus: false // set focus to editable area after initializing summernote
            });
        });
    </script>
@endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
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

    <div class="card">
        <div class="card-body">
			<h4 class="card-title">Editar</h4>
			<form class="mt-4" action="{{ url("/emails/update/{$data['email']['id']}") }}" method="post">
                @csrf
       			@include('emails._form')
                <button type="submit" class="btn btn-primary">Editar e-mail</button>
       		</form>
        </div>
    </div>

@endsection
