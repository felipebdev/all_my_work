@extends('templates.horizontal.main')

@section('jquery')
@endsection

@push('before-styles')
@endpush

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Assinaturas</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="javascript:void(0)">Configurações</a>
                </li>
                <li class="breadcrumb-item active">Assinaturas</li>
            </ol>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="{{ route('services.create') }}" class="btn btn-rounded btn-outline-primary">
                    <i class="fa fa-star"></i> Nova Assinatura
                </a>
            </div>
            <div class="table-responsive m-t-30">
                @if ($errors->any())
                    <div class="alert alert-warning">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th width="20%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                        <tr>
                            <td>{{ ($service->type !== 'plan') ? 'Addon' : 'Plano' }}</td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->price }}</td>
                            <td>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('services.edit', ['uuid' => $service->id]) }}"
                                        class="btn btn-rounded btn-outline-primary">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                    <form method="POST"
                                        onsubmit="return confirm('Deseja deletar a assinatura: {{ addslashes($service->name) }} ?')"
                                        action="{{ route('services.delete', ['uuid' => $service->id]) }}">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-rounded btn-outline-danger">
                                            <i class="fa fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">Não há assinaturas cadastradas</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection