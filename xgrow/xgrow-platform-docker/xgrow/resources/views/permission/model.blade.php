@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/permissions_edit.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/permission_list.js') }}"></script>
@endpush

@push('jquery')
    <script>
        let permission_id = {{ $permission->id }}
        let user_id = {{ Auth::user()->id }}
        let users_permission = [];

        $(function() {
            getUsers(permission_id);
            $(`#move_all_user_0`).hide();
            $(`#move_all_user_1`).hide();
        });

        function getUsers(permission_id) {
            $.ajax({
                url: "{{ route('permission.get_users') }}",
                method: "GET",
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    permission_id
                },
                success: function(response) {
                    users_permission = response.users_permission;
                    showUsers();
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        function showUsers() {
            for (i = 0; i <= 1; i++) {
                const users = users_permission[i];
                let content = "";
                if (users.length > 0) {
                    users.forEach(user => {
                        const {
                            id,
                            name
                        } = user;
                        content += `
                                    <li class="draggable-list-group-item list-group-item ${(id == user_id) ? 'static' : ''}" moveid="${id}" moveidx="${+!i}">
                                        <div class="mb-2 p-2 card-thin-full">
                                            <div class="card-thin-full-heading">
                                                <div class="xgrow-check d-flex align-items-center">
                                                    ${(id != user_id) ? `<input type="checkbox" id="user_${id}" class="mx-2 user_${i}" value="${id}">` : `<input type="checkbox" id="user_${id}" class="mx-2 user_${i}" value="${id}" title="Esse sou eu!" disabled>` }
                                                    <input type="hidden" name="users[${i}][]" value="${id}">
                                                    <label for="user_${id}">${name}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                `;
                    });
                    $(`#move_all_user_${i}`).show();
                } else {
                    $(`#move_all_user_${i}`).hide();
                }
                $(`#users_list_${i}`).empty().append(content);
            }
        }

        function moveUser(id, move) {
            current = +!(Number(move));
            users_permission[move].push(users_permission[current].find(user => user.id == id));
            users_permission[current] = users_permission[current].filter(user => user.id != id);
            showUsers();
        }

        function moveAllUser(move) {
            current = +!move;
            const selected_users = $(`.user_${current}:checked`);
            if (selected_users.length > 0) {
                selected_users.each(
                    (key, user) => {
                        moveUser(user.value, move);
                    }
                );
            } else {
                errorToast('Erro ao mover', 'Selecione ao menos um usuário');
            }
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/permission">Permissões</a></li>
            <li class="breadcrumb-item active mx-2"><span>{{ $permission->id == 0 ? 'Criar' : 'Editar' }} grupo</span>
            </li>
        </ol>
    </nav>
    @include('elements.alert')
    <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link active" id="nav-data-tab" data-bs-toggle="tab" href="#nav-permissions"
           role="tab" aria-controls="nav-permissions" aria-selected="true">Grupo</a>
        <a class="xgrow-tab-item nav-item nav-link" id="nav-users-tab" data-bs-toggle="tab" href="#nav-users" role="tab"
           aria-controls="nav-users" aria-selected="false">Usuários</a>
    </div>
    @if ($permission->id > 0)
        {!! Form::model($permission, ['method' => 'put', 'route' => ['permission.update', $permission->id]]) !!}
    @else
        {!! Form::open(['route' => 'permission.store']) !!}
    @endif
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-permissions" role="tabpanel" aria-labelledby="nav-data-permissions">
            @include('permission._tab-permission')
        </div>
        <div class="tab-pane fade" id="nav-users" role="tabpanel" aria-labelledby="nav-users-tab">
            @include('permission._tab-users')
        </div>
    </div>
    {!! Form::close() !!}
    @include('elements.toast')
@endsection
