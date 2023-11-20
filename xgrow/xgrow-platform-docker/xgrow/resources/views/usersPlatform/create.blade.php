@extends('templates.xgrow.main')

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
@endpush

@push('jquery')
    <script>
        user_verify = 0;

        function newVerifyIsNecessary() {
            $('#data_user').addClass('d-none')
            $('#user_registered').addClass('d-none')
            $('#user_not_registered').addClass('d-none')
        }

        $('#user_email').on('change', function () {
            user_verify = 0;
            newVerifyIsNecessary()
        });

        $('#btn_user_verify').on('click',
            function () {
                email = $('#user_email').val();
                user_verify = 1;
                $.ajax({
                    type: 'POST',
                    url: "{{ route('platforms-users.verify') }}",
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        email,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function (data, statusText, xhr) {
                        $('#type_access').removeClass('d-none')
                        $('#user_password, #user_password-repeat').val("")
                        let type_access = 'restrict'
                        let permission_id = 0
                        if (xhr.status === 200) {
                            type_access = data.user.type_access
                            permission_id = data.user.permission_id
                            $(`#type_access_${type_access}`).prop('checked', true)
                            $('#data_user').addClass('d-none')
                            $('#save-user').text('Vincular')
                            $('#data_type').val('edit');
                            $('#label_user_name').hide()
                            $('#user_name').val(data.user.name)
                            $('#user_registered').removeClass('d-none')
                            $('#user_not_registered').addClass('d-none')
                            $('#keep_password').removeClass('d-none')
                        } else {
                            $('#type_access_restrict').prop('checked', true)
                            $('#data_user').removeClass('d-none')
                            $('#label_user_name').show()
                            $('#data_type').val('create');
                            $('#user_name').val("")
                            $('#user_registered').addClass('d-none')
                            $('#user_not_registered').removeClass('d-none')
                            $('#keep_password').addClass('d-none')
                            $('#save-user').text('Salvar')
                        }

                        if(type_access === 'full')
                            $('#div_permission_id').addClass('d-none')
                        else
                            $('#div_permission_id').removeClass('d-none')

                        $('#permission_id').val(permission_id)

                    },
                    error: function (data) {
                        errorToast('Erro', 'Erro desconhecido. Contacte o suporte');
                    },
                });
            }
        )
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/platform-config/users">Usuários</a></li>
            <li class="breadcrumb-item active mx-2"><span>Novo Usuário</span></li>
        </ol>
    </nav>

    @include('usersPlatform.form')
    @include('elements.toast')
@endsection
