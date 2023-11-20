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


    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

    <!--
        content -->
    <script>
        function deleteContent(r, id, title) {
            if (!confirm(`Deseja excluir o conteúdo ${title}?`)) {
                return false
            } else {
                var i = r.parentNode.parentNode.parentNode.rowIndex;
                $.ajax({
                    type: 'GET',
                    url: "content/destroy/" + id,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'id': id,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        document.getElementById("content-table").deleteRow(i);
                        toastr["success"]("Registro excluído com sucesso!");
                    },
                    error: function(data) {
                        toastr["error"]("Houve um erro na exclusão do registro: " + data.responseJSON.message);
                    }
                });
            }


        }

    </script>


    <!-- Subscribers -->
    <script>
        $('.tables').DataTable({
            scrollX: false,
            language: {
                "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Portuguese-Brasil.json"
            },
        });

        function deleteSubscriber(r, id, name) {
            if (!confirm(`Deseja excluir o assinante ${name}?`)) {
                return false
            } else {
                var i = r.parentNode.parentNode.parentNode.rowIndex;
                $.ajax({
                    type: 'POST',
                    url: "{{ URL::route('subscribers.destroy') }}",
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'id': id,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        document.getElementById("subscriber-table").deleteRow(i);
                        toastr["success"]("Registro excluído com sucesso");
                    },
                    error: function(data) {
                        toastr["error"]("Houve um erro na exclusão do registro: " + data.responseJSON.message);
                    }
                });
            }
        }

    </script>

    <script>
        function changeStatusAuthors(id) {
            $.ajax({
                url: `/authors/${id}/status`,
                type: 'PUT',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(data) {
                    console.log(data)
                }
            });
        }

    </script>

@endpush



@section('content')
    <!--
        Subscribers -->
    <div class="card">
        <div class="card-body">
            <h2>Assinantes</h2>
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

                <table id="subscriber-table" class="table fandone-table tables">
                    <thead class="default-background text-white">
                        <tr>
                            <th>Assinante</th>
                            <th>Nome</th>
                            <th>Cadastro</th>
                            <th>Status</th>
                            <th>Útimo Acesso</th>
                            <th>Plano</th>
                            <th>Score</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        @foreach ($subscribers as $subscriber)
                            <tr>
                                <td>{{ $subscriber->id }}</td>
                                <td>{{ $subscriber->name }}</td>
                                <td>
                                    @if ($subscriber->created_at != null)
                                        {{ date('d/m/Y', strtotime($subscriber->created_at)) }}@else @endif
                                </td>
                                <td>{{ $subscriber->status }}</td>
                                <td>
                                    @if ($subscriber->last_acess != null)
                                        {{ date('d/m/Y H:i', strtotime($subscriber->last_acess)) }}@else @endif
                                </td>
                                <td>{{ $subscriber->plan_name }}</td>
                                <td>?</td>
                                <td>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ url("/subscribers/{$subscriber->id}/edit") }}" class="fandone-edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="#" class="fandone-delete subscriber-trash"
                                            onclick="deleteSubscriber(this,{{ $subscriber->id }},'{{ $subscriber->name }}')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--
        contents -->
    <div class="card">
        <div class="card-body">
            <h2>Conteudos</h2>
            <div class="table-responsive m-t-30">
                <table id="content-table" class="table fandone-table tables">
                    <thead class="default-background text-white">
                        <tr>
                            <th>Data Publicação</th>
                            <th>Título</th>
                            <th>Seção</th>
                            <th>Status</th>
                            <th>Autor</th>
                            <th>Views</th>
                            <th>Likes</th>
                            <th>Comentários</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contents as $content)
                            <tr>
                                <td>
                                @if ($content->published_at == null) Indefinida @else
                                        {{ date('d/m/Y', strtotime($content->published_at)) }} @endif
                                </td>
                                <td>{{ $content->title }}</td>
                                <td>{{ $content->name_section }}</td>
                                <td>
                                @if ($content->status == null) Inativo @else Ativo
                                    @endif
                                </td>
                                <td>{{ $content->name_author }}</td>
                                <td>{{ $content->views }}</td>
                                <td>{{ $content->likes }}</td>
                                <td>Comentario</td>
                                <td>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ url("/content/{$content->id}/edit") }}" class="fandone-edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="#" class="fandone-delete content-trash"
                                            onclick="deleteContent(this,{{ $content->id }},'{{ $content->title }}')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>





    <!--
        Authors -->
    <div class="card">
        <div class="card-body">
            <h2>Autores</h2>
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
                <table id="plan-table" class="table fandone-table">
                    <thead class="default-background text-white">
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>E-mail</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($authors as $author)
                            <tr>
                                <td>{{ $author->name_author }}</td>
                                <td>{{ $author->author_desc }}</td>
                                <td>{{ $author->author_email }}</td>
                                <td>
                                    <span class="d-none">{{ $author->status === '1' ? 'Ativo' : 'Inativo' }}</span>
                                    <div class="ckbx-style-8">

                                        <input type="checkbox" id="ckbx-style-1-{{ $author->id }}"
                                            value="{{ $author->status }}" name="ckbx-style-1"
                                            {{ $author->status ? 'checked' : '' }}
                                            onclick="changeStatus({{ $author->id }})">

                                        <label for="ckbx-style-1-{{ $author->id }}"></label>

                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ url("/authors/{$author->id}/edit") }}" class="fandone-edit">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <form method="POST"
                                            onsubmit="return confirm('Deseja excluir o autor {{ addslashes($author->name_author) }}?')"
                                            action="{{ url("/authors/{$author->id}") }}">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="fandone-delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>




@endsection
