@extends('templates.horizontal.main')

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
<script>
    $(function() {
    $('#gallery-table').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
        language: {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
        }
    });

    $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('btn btn-primary mr-1');
});
  
</script>

@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Galeria</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Álbuns</li>
        </ol>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-end">
            <a href="{{ route('gallery.create') }}" class="btn btn-rounded btn-outline-primary">
                <i class="fa fa-star"></i> Novo Álbum
            </a>
        </div>
        <div class="table-responsive m-t-30">
            <table id="gallery-table" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Imagens</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                     @forelse ($galleries as $gallery)
                    <tr>
                        <td>{{ $gallery->name }}</td>
                        <td>{{ $gallery->description }}</td>
                        <td class="d-flex justify-content-center">{{ $gallery->images()->count() }}</td>
                        <td>
                            <a href="{{ route('gallery.image.index', [$gallery->id]) }}" class="btn btn-rounded btn-outline-primary">
                                    <i class="fa fa-upload"></i> Imagens
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('gallery.edit', [$gallery->id]) }}" class="btn btn-rounded btn-outline-primary">
                                    <i class="fa fa-edit"></i> Editar
                            </a>
                        </td>
                        <td>
                            @if($gallery->images()->count() == 0)
                                <a href="{{ route('gallery.destroy', [$gallery->id]) }}" class="btn btn-rounded btn-outline-danger">
                                    <i class="fa fa-trash"></i> Excluir
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Não há álbum cadastrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection