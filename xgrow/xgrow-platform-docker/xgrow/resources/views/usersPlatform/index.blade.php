@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
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

    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>

    <script>
        $('.tb-edit-xgrow').on('click', function(evt) {
            let user_url = '';

            if (evt.target.nodeName == 'BUTTON') {
                user_url = evt.target.getAttribute('data-user-url');
            } else {
                user_url = evt.target.parentElement.getAttribute('data-user-url');
            }

            window.location = user_url;
        });

        $('.tb-exclude-xgrow').on('click', function(evt) {
            let user_url = '';

            if (evt.target.nodeName == 'BUTTON') {
                user_url = evt.target.getAttribute('data-user-url');
            } else {
                user_url = evt.target.parentElement.getAttribute('data-user-url');
            }

            document.querySelector('#form_exclude').setAttribute('action', user_url);
        });

        // DATA TABLE
        let dataTableOpts = {
            dom: '<"d-flex flex-wrap justify-content-between align-items-center"' +
                '<"title-table"><"create-button mb-2">>' +
                '<"filter-div mt-2"><"mt-2" rt>' +
                '<"my-3 d-flex flex-wrap align-items-center justify-content-between"<"my-2"l><"my-2"p>>',
            lengthMenu: [
                [10, 25, 50, -1],
                ['10 itens por página', '25 itens por página', '50 itens por página', 'Todos os registros']
            ],
            processing: true,
            serverSide: false,
            fixedHeader: true,
            language: {
                'url': "{{ asset('js/datatable-translate-pt-BR.json') }}",
            },
            buttons: [{
                    extend: 'pdf',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em PDF">\n' +
                        '                  <i class="fa fa-file-pdf" style="color: red"></i>\n' +
                        '                </button>',
                    className: '',
                    exportOptions: {
                        columns: [':visible:not(.no-export)'],
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                },
                {
                    extend: 'csv',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em CSV">\n' +
                        '                  <i class="fas fa-file-csv" style="color: blue"></i>\n' +
                        '                </button>',
                    className: '',
                    exportOptions: {
                        columns: [':visible:not(.no-export)'],
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                },
                {
                    extend: 'excel',
                    text: '<button class="xgrow-button export-button me-1" title="Exportar em XLSX">\n' +
                        '                  <i class="fas fa-file-excel" style="color: green"></i>\n' +
                        '                </button>',
                    className: '',
                    exportOptions: {
                        columns: [':visible:not(.no-export)'],
                        modifier: {
                            selected: true,
                            page: 'all'
                        }
                    },
                },
            ],
            initComplete: function(settings, json) {
                $('.title-table').html(
                    '<h5 class="align-self-center">Usuários: <span id="spn-total-label">{{ count($users) }}</span></h5>'
                );
                $('.buttons-csv').removeClass('dt-button buttons-csv');
                $('.buttons-excel').removeClass('dt-button buttons-excel');
                $('.buttons-pdf').removeClass('dt-button buttons-pdf');
                $('.create-button').html(
                    '<button onclick="location.href=\'/platform-config/users/create\'" class="xgrow-button" style="height:40px; width:128px"><i class="fa fa-plus"></i> Novo usuário </button>'
                );
            },
        };

        function invokeDelete(id, additionalDescription = '') {
            const deleteRoute = @json(route('platforms-users.destroy', ':id'));
            const deleteUrl = deleteRoute.replace(/:id/g, id);

            let desc = additionalDescription ? (': ' + additionalDescription) : ''
            const modalOptions = {
                title: 'Excluir usuário',
                description: 'Você tem certeza que deseja excluir o usuário' + desc + '?',
                btnSave: 'Sim, excluir',
                btnCancel: 'Não, manter',
                success: 'Usuário excluído com sucesso',
                error: 'Não foi possível excluir o usuário: ',
                url: deleteUrl,
                method: 'DELETE',
                body: {
                    'id': id,
                    '_token': "{{ csrf_token() }}",
                },
                datatables: 'users-table'
            }
            openConfirmationModal(window.btoa(JSON.stringify(modalOptions)))
        }

        $(document).ready(function() {
            $("#users-table").DataTable(dataTableOpts);
        });

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item active mx-2"><span>Usuários</span></li>
        </ol>
    </nav>

    @include('elements.alert')

    <div class="wide-view xgrow-card card-dark p-0">
        <div class="xgrow-card-body px-3 py-4">
            <div class="table-responsive m-t-30">
                <table id="users-table"
                    class="xgrow-table table text-light table-responsive dataTable overflow-auto no-footer"
                    style="width:100%">
                    <thead>
                        <tr class="card-black" style="border: 4px solid var(--black-card-color)">
                            <th scope="col">Nome</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Tipo de acesso</th>
                            <th scope="col">Permissão</th>
                            <th class="no-export"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr id="card_{{$user->id}}">
                                <td>
                                    {{ $user->name }}
                                </td>
                                <td>
                                    {{ $user->email }}
                                </td>
                                <td>
                                    {{ $user->type_access == 'full' ? 'Total' : 'Restrito' }}
                                </td>
                                <td>
                                    @if( isset($owner->email) &&  $user->email == $owner->email )
                                        Proprietário
                                    @else
                                        {{ (is_null($user->permission) && $user->type_access == 'full')? 'Total' : $user->permission }}
                                    @endif
                                </td>
                                <td>
                                    @if( isset($owner->email) &&  $user->email != $owner->email )
                                    <div class="dropdown">
                                        <button class="xgrow-button table-action-button m-1" type="button"
                                            id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu table-menu"
                                            aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                            <li><a class="dropdown-item table-menu-item"
                                                    href="{{ route('platforms-users.edit', [$user->id]) }}">Editar</a>
                                            </li>
                                            <li><a class="dropdown-item table-menu-item" href="javascript:void(0)" onclick="invokeDelete({{ $user->id }})">Excluir</a>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('elements.confirmation-modal')
    @include('elements.toast')
@endsection
