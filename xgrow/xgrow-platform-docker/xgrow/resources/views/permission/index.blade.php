@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />

    <style>
        .title-table {
            display: flex;
            align-self: center;
            justify-content: center;
        }

    </style>
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

    <script>
        $('.tb-exclude-xgrow').on('click', function(evt) {
            let permission_url = '';

            if (evt.target.nodeName == 'BUTTON') {
                permission_url = evt.target.getAttribute('data-permission-url');
            } else {
                permission_url = evt.target.parentElement.getAttribute('data-permission-url');
            }

            document.querySelector('#modal_permission_delete').setAttribute('action', permission_url);
        });

        let dataTableOpts = {
            "dom": '<"mt-2" rt>' +
                '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
            lengthMenu: [
                [10, 25, 50, -1],
                ['10 itens por página', '25 itens por página', '50 itens por página', 'Todos os registros']
            ],
            processing: true,
            searching: false,
            info: true,
            language: {
                'url': "{{ asset('js/datatable-translate-pt-BR.json') }}",
            },

        };

        $(document).ready(function() {
            $("#permissions-table").DataTable(dataTableOpts);
        });

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item active mx-2"><span>Permissões</span></li>
        </ol>
    </nav>

    <!-- MODAL -->
    <div class="modal-sections modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>

                <div class="modal-header">
                    <p class="modal-title" id="exampleModalLabel">Excluir permissão</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir esta permissão?
                </div>
                <div class="modal-footer">
                    <form id="modal_permission_delete" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-success"
                            onclick="document.querySelector('#modal_permission_delete').submit()" data-bs-dismiss="modal"
                            aria-label="Close">
                            Sim, excluir
                        </button>
                    </form>
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                        Não, manter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Default toast -->
    <div aria-live="polite" aria-atomic="true" class="d-flex justify-content-center align-items-center w-100">
        <div class="xgrow-toast toast align-items-center text-white border-0" id="sucessToast" role="alert"
            aria-live="assertive" aria-atomic="true">
            <div class="xgrow-toast-header toast-header text-white border-0">
                <strong class="me-auto">Permissão excluída</strong>
                <button type="button" class="xgrow-toast-btn-close toast-btn-close btn-close ms-auto me-2"
                    data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="xgrow-toast-body toast-body">Esta permissão não está mais disponível</div>
        </div>
    </div>
    <!-- End of default toast -->


    <div class=" wide-view xgrow-card card-dark p-0">
        <div class="xgrow-card-body px-3 py-4">
            <div class="table-responsive m-t-30">

                <table id="permissions-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">

                    <div
                        class="d-flex flex-wrap justify-content-center justify-content-xl-between justify-content-lg-center">
                        <div class="title-table me-1">
                            <h5 class="align-self-center">Permissões: <span
                                    id="spn-total-label">{{ $total_label > 1 ? $total_label . ' permissões' : $total_label . ' permissão' }}</span>
                            </h5>
                        </div>

                        <div
                            class="d-flex flex-wrap align-items-center justify-content-xl-between justify-content-lg-center">
                            <div class="d-flex flex-wrap align-items-center justify-content-center mb-2">

                                <div class="global-search">
                                    <div class="xgrow-input me-1 pt-0" style="background-color: var(--input-bg); height: 40px;">
                                        <input id="ipt-global-filter" placeholder="Busque alguma coisa..." type="text"
                                            style="height: 40px;">
                                        <span class="xgrow-input-cancel"><i class="fa fa-search"
                                                aria-hidden="true"></i></span>
                                    </div>
                                </div>
                                <div class="filter-button">
                                    <div class="d-flex align-items-center py-2">
                                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample"
                                            aria-bs-expanded="false" aria-bs-controls="collapseExample"
                                            class="xgrow-button-filter xgrow-button export-button me-1"
                                            aria-expanded="true">
                                            <p>Filtros avançados <i class="fa fa-chevron-down" aria-hidden="true"></i></p>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap mt-2">
                                    <div class="buttons-export">
                                        <button class="xgrow-button export-button me-1" title="Exportar em PDF">
                                            <i class="fas fa-file-pdf" style="color: red"></i>
                                        </button>
                                        <button class="xgrow-button export-button me-1" title="Exportar em CSV">
                                            <i class="fas fa-file-csv" style="color: blue"></i>
                                        </button>
                                        <button class="xgrow-button export-button me-1" title="Exportar em XLSX">
                                            <i class="fas fa-file-excel" style="color: green"></i>
                                        </button>
                                    </div>
                                    <div class="create-button mb-2">
                                        <button class="xgrow-button" type="submit" style="height: 40px;"
                                            onclick="location.href='{{ url('/permission/create') }}'"><i
                                                class="fa fa-plus"></i> Nova permissão</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="filter-div mt-2">
                        <div class="mb-3 collapse" id="collapseExample">
                            <div class="filter-container">
                                <div class="p-2 px-3">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3 mt-1">
                                            <p>Filter goes here</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <thead>
                <tr class="card-black" style="border: 4px solid var(--black-card-color);">
                    <th scope="col">Grupo</th>
                    <th scope="col">Atribuições</th>
                    <th scope="col">Usuários</th>
                    <th scope="col" style="text-align-last: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- ------------------------------------------------------------------------------------------------------ -->
                @foreach ($permissions as $permission)
                    <tr>
                        <td>
                            <p>{{ $permission->name }}</p>
                        </td>
                        <td>
                            @foreach ($permission->roles()->get() as $key => $role)@if($key > 0), @endif{{ $role->name }}@endforeach
                        </td>
                        <td>
                            @foreach ($permission->platformusers()->get() as $key => $user)@if($key > 0), @endif{{ $user->name }}@endforeach
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <button class="xgrow-button table-action-button mx-2"
                                    onclick="location.href='{{ route('permission.edit', [$permission->id]) }}'">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="xgrow-button table-action-button tb-exclude-xgrow mx-2"
                                    data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    data-permission-url="{{ route('permission.destroy', [$permission->id]) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                <!-- ------------------------------------------------------------------------------------------------------ -->
            </tbody>
            </table>
        </div>
    </div>

    </div>
@endsection
