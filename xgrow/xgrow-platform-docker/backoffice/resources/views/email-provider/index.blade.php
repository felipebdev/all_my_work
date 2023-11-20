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
    <!-- end - This is for export functionality only -->
@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Provedores de e-mail</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item">Configurações</li>
                <li class="breadcrumb-item active">Provedores de e-mail</li>
            </ol>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST"
                  onsubmit="return confirm('Deseja realmente alterar o provedor padrão?')"
                  action="{{route('email-provider.apply')}}">
                {{ csrf_field() }}
                {{ method_field('POST') }}

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="default-provider">Provedor padrão</label>
                            <input type="text" class="form-control" id="default-provider" name="default-provider"
                                   value="{{$defaultProvider ?? 'Sem provedor padrão definido' }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cached-provider">Provedor no cache</label>
                            <input type="text" class="form-control" id="cached-provider" name="cached-provider"
                                   value="{{$cachedProvider ?? 'Não armazenado no cache' }}" disabled>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="from_address">Alterar provedor para</label>
                            <select class="form-control" name="provider" required>
                                <option value="" ></option>
                                @foreach($providers as $provider)
                                    <option value="{{$provider->name }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="">&nbsp;</label><br>
                        <button type="submit" class="btn btn-rounded btn-outline-warning">
                            <i class="fa fa-trash"></i> Alterar provedor padrão
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <span class="text-warning">Atenção</span>: devido à forma como o cache funciona, a operação irá apenas alterar o provedor
                        padrão e remover do cache. O cache é refeito somente quando a aplicação solicitar os dados
                        novamente.
                    </div>
                </div>
            </form>

            <hr>

            <div class="d-flex justify-content-end">
                <a href="{{ route('email-provider.create') }}" class="btn btn-rounded btn-outline-primary">
                    <i class="fa fa-star"></i> Novo provedor
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

                <table id="platform-table" class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Id do provedor</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>From name</th>
                        <th>From address</th>
                        <th>Tags</th>
                        <th>Driver</th>
                        <th>Configurações</th>
                        <th width="20%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($providers as $item)
                        <tr>
                            <td>{{ $item->id}}</td>
                            <td>{{ $item->name}}</td>
                            <td>{{ $item->description}}</td>
                            <td>{{ $item->from_name}}</td>
                            <td>{{ $item->from_address}}</td>
                            <td>{{ $item->service_tags}}</td>
                            <td>{{ $item->driver}}</td>
                            <td>{{ $item->settings}}</td>
                            <td>
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('email-provider.edit',  ['provider' => $item->id])}}"
                                       class="btn btn-rounded btn-outline-primary">
                                        <i class="fa fa-edit"></i> Editar
                                    </a>
                                    <form method="POST"
                                          onsubmit="return confirm('Deseja deletar o provedor: {{addslashes($item->id)}} ?')"
                                          action="{{route('email-provider.destroy', ['provider' => $item->id])}}">
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
                            <td colspan="8" class="text-center">Não há provedores cadastrados</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endsection
