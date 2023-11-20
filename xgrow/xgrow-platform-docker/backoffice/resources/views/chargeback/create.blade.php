@extends('templates.horizontal.main')

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')
    <script>
        $(document).ready(function() {

            $('#chargebackForm').submit(function(e){
                e.preventDefault();
                const file_data = $('#chargebackFile').prop('files')[0];
                const form_data = new FormData();
                form_data.append('file', file_data);
                const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
                form_data.append('_token',CSRF_TOKEN);
                $.ajax({
                    url: "{{ route('chargeback.store') }}",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function(response){
                        console.log(response)
                        $('#chargebackError').addClass('d-none');
                        $('#chargebackSuccess').removeClass('d-none');
                        $('#chargebackSuccess').html(`
                            Relatorio gerado com sucesso.<br />
                            <a href="${response.response.link}" target="_blank">[Clique aqui para realizar o Download]</a>
                        `)
                    },
                    error: function(response, status, error) {
                        $('#chargebackError').removeClass('d-none');
                        $('#chargebackSuccess').addClass('d-none');
                        $('#chargebackError').html(response.responseJSON.message)
                    }
                });
            })

        })
    </script>
@endpush

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Template</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Relatórios</a></li>
                <li class="breadcrumb-item active">Estorno</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <h4 class="card-title">Gerar relatório de estorno</h4>


            <form id="chargebackForm">

                <div class="row">

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('file','Arquivo:') !!}
                            {!!
                                Form::file('file',
                                            [
                                                'class' => 'form-control',
                                                'id' => 'chargebackFile'
                                            ]
                                            )
                            !!}
                        </div>
                    </div>

                    <div class="col-md-6">

                        <div class="d-flex justify-content-center">
                            <div id="chargebackSuccess" class="alert alert-success d-none">
                            </div>
                            <div id="chargebackError" class="alert alert-danger d-none">
                            </div>
                        </div>

                    </div>

                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class='form-group'>
                            {!! Form::submit('Salvar',[
                                        'class'=>'btn btn-primary btn-primary-custom'
                             ]) !!}
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>

@endsection
