@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
    <style>
        .xgrow-input-delete {
            background: var(--black-card-color);
            padding: 5px 10px;
            border-radius: 3px;
            color: var(--red);
        }
    </style>
@endpush

@push('after-scripts')
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/confirmation-modal.js') }}"></script>
    <script src="//cdn.muicss.com/mui-0.10.3/js/mui.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"
        integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw=="
        crossorigin="anonymous"></script>
    <script>
        let newipHelper = 0;
        let newReasonLossHelper = 0;
        let newReasonGainHelper = 0;
        $(function() {
            $('.xgrow-datepicker').datepicker({
                format: 'dd/mm/yyyy',
                language: 'pt-BR',
                autoclose: true
            });

            $('#initial_hour').mask('00:00');
            $('#final_hour').mask('00:00');

            const checks = [
                {
                    name: 'period_restriction',
                    value: '{{ $config->period_restriction }}'
                },
                {
                    name: 'ip_restriction',
                    value: '{{ $config->ip_restriction }}'
                },
                {
                    name: 'limit_leads',
                    value: '{{ $config->limit_leads }}'
                },
                {
                    name: 'allow_reasons_loss',
                    value: '{{ $config->allow_reasons_loss }}'
                },
                {
                    name: 'allow_reasons_gain',
                    value: '{{ $config->allow_reasons_gain }}'
                }
            ];

            for (let i = 0; i < checks.length; i++) {
                if (checks[i].value == false && !$(`#${checks[i].name}`).is(':checked')) {
                    $(`.${checks[i].name}`).each(function() {
                        $(this).attr('disabled', 'disbled');
                    });
                }

                $(`#${checks[i].name}`).change(function () {
                    if ($(this).is(':checked')) {
                        $(`.${checks[i].name}`).each(function() {
                            $(this).removeAttr('disabled');
                        });
                    } else {
                        $(`.${checks[i].name}`).each(function() {
                            $(this).attr('disabled', 'disbled');
                        });
                    }
                });
            }

            $('.add_new_ip').click(function () {
                let newip =
                `<div class="col-lg-3 col-md-3 col-sm-6 col-12 ip-element-n${newipHelper}">
                    <a class="xgrow-input-delete mb-1 ip_restriction delete_ip" data-ip-id="n${newipHelper}" href="javascript:void(0)">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <input id="newip-${newipHelper}" autocomplete="off" spellcheck="false" class="" name="newip[]" type="text">
                        <label for="newip-${newipHelper}">Endereço de IP</label>
                    </div>
                </div>`;

                newipHelper++;

                $('.add_new_ip_div').before(newip);
            });

            $('.ip_addresses_container').on('click', '.delete_ip', function () {
                if ($(this).attr('disabled')) return;
                let ip_to_delete = $(this).attr('data-ip-id');
                
                if (ip_to_delete[0] != 'n') {
                    let ips = $('#ip_to_delete').val();
                    ips = (ips != '' ? ips+'|' : '') + ip_to_delete;
                    $('#ip_to_delete').val(ips);
                }

                $(`.ip-element-${ip_to_delete}`).remove();
            });

            $('.add_new_reason_loss').click(function () {
                let newReasonLoss =
                `<div class="col-lg-3 col-md-3 col-sm-6 col-12 reason-loss-element-n${newReasonLossHelper}">
                    <a class="xgrow-input-delete mb-1 ip_restriction delete_reason_loss" data-reason-loss-id="n${newReasonLossHelper}" href="javascript:void(0)">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <input id="newreasonloss-${newReasonLossHelper}" autocomplete="off" spellcheck="false" class="" name="newreasonloss[]" type="text">
                        <label for="newreasonloss-${newReasonLossHelper}">Motivo</label>
                    </div>
                </div>`;

                newReasonLossHelper++;

                $('.add_new_reason_loss_div').before(newReasonLoss);
            });

            $('.reasons_container_loss').on('click', '.delete_reason_loss', function () {
                if ($(this).attr('disabled')) return;
                let reason_loss_to_delete = $(this).attr('data-reason-loss-id');
                
                if (reason_loss_to_delete[0] != 'n') {
                    let reasons = $('#reason_loss_to_delete').val();
                    reasons = (reasons != '' ? reasons+'|' : '') + reason_loss_to_delete;
                    $('#reason_loss_to_delete').val(reasons);
                }

                $(`.reason-loss-element-${reason_loss_to_delete}`).remove();
            });


            $('.add_new_reason_gain').click(function () {
                let newReasonGain =
                `<div class="col-lg-3 col-md-3 col-sm-6 col-12 reason-gain-element-n${newReasonGainHelper}">
                    <a class="xgrow-input-delete mb-1 ip_restriction delete_reason_gain" data-reason-gain-id="n${newReasonGainHelper}" href="javascript:void(0)">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <input id="newreasongain-${newReasonGainHelper}" autocomplete="off" spellcheck="false" class="" name="newreasongain[]" type="text">
                        <label for="newreasongain-${newReasonGainHelper}">Motivo</label>
                    </div>
                </div>`;

                newReasonGainHelper++;

                $('.add_new_reason_gain_div').before(newReasonGain);
            });

            $('.reasons_container_gain').on('click', '.delete_reason_gain', function () {
                if ($(this).attr('disabled')) return;
                let reason_gain_to_delete = $(this).attr('data-reason-gain-id');
                
                if (reason_gain_to_delete[0] != 'n') {
                    let reasons = $('#reason_gain_to_delete').val();
                    reasons = (reasons != '' ? reasons+'|' : '') + reason_gain_to_delete;
                    $('#reason_gain_to_delete').val(reasons);
                }

                $(`.reason-gain-element-${reason_gain_to_delete}`).remove();
            });

        });
    </script>
@endpush

{{-- @php
    dd($config, $restrictedIp, $reasonsLoss);
@endphp --}}

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('callcenter.dashboard') }}">Call center</a></li>
            <li class="breadcrumb-item active mx-2"><span>Configurações</span></li>
        </ol>
    </nav>

    @include('elements.alert')

    <div class="xgrow-card card-dark p-3 py-4">
        <form action="{{ route('callcenter.config.update', $config->id) }}" method="POST">
            <div class="xgrow-card-header pb-3 mb-3">
                <div class="d-flex align-items-center px-3">
                    <div class="form-check form-switch">
                        {!! Form::checkbox('active', true, (old('active', $config->active ?? false)), ['id' => 'active', 'class' => 'form-check-input']) !!}
                        {!! Form::label('active', 'Ativar Call Center', ['class' => 'form-check-label']) !!}
                    </div>
                </div>
            </div>
            <hr class="mt-0" style="border-color: var(--border-color)"/>
            <div class="xgrow-card-body p-3">
                <h5 class="xgrow-card-title my-2" style="font-size: 1.5rem; line-height: inherit">
                    Restrições
                </h5>
    
                {{-- RESTRIÇÃO POR PERÍODO --}}
                <div class="row my-2">
                    <div class="col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('period_restriction', true, (old('period_restriction', $config->period_restriction ?? true)), ['id' => 'period_restriction', 'class' => 'form-check-input']) !!}
                            {!! Form::label('period_restriction', 'Restrição por período', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input type="text" name="initial_date" id="initial_date" class="custom-datepicker xgrow-datepicker period_restriction"
                                   value="{{ $config->initial_date ? DateTime::createFromFormat("Y-m-d H:i:s", $config->initial_date)->format('d/m/Y') : '' }}">
                            {!! Form::label('initial_date', 'Data inicial') !!}
                        </div>
                    </div>
    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                        <div class="xgrow-form-control mb-3">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::text('initial_hour', $config->initial_hour ?? '', ['id' => 'initial_hour', 'class' => 'period_restriction']) !!}
                                {!! Form::label('initial_hour', 'Hora inicial') !!}
                            </div>
                        </div>
                    </div>
    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input type="text" name="final_date" id="final_date" class="custom-datepicker xgrow-datepicker period_restriction"
                                   value="{{ $config->final_date ? DateTime::createFromFormat("Y-m-d H:i:s", $config->final_date)->format('d/m/Y') : '' }}">
                            {!! Form::label('final_date', 'Data final') !!}
                        </div>
                    </div>
    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                        <div class="xgrow-form-control mb-3">
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                                {!! Form::text('final_hour', $config->final_hour ?? '', ['id' => 'final_hour', 'class' => 'period_restriction']) !!}
                                {!! Form::label('final_hour', 'Hora final') !!}
                            </div>
                        </div>
                    </div>
                </div>
    
                {{-- RESTRIÇÃO POR IP --}}
                <div class="row my-2">
                    <div class="col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('ip_restriction', true, (old('ip_restriction', $config->ip_restriction ?? true)), ['id' => 'ip_restriction', 'class' => 'form-check-input']) !!}
                            {!! Form::label('ip_restriction', 'Restrição por IP', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="row my-2 align-items-end ip_addresses_container">
                    @foreach ($restrictedIp as $ip)
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12 ip-element-{{ $ip->id }}">
                            <a class="xgrow-input-delete mb-1 ip_restriction delete_ip" data-ip-id="{{ $ip->id }}" href="javascript:void(0)">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                                {!! Form::text("ip[$ip->id]", $ip->ip_address, ['id' => "ip-$ip->id", 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine ip_restriction']) !!}
                                {!! Form::label("ip-$ip->id", 'Endereço de IP') !!}
                            </div>
                        </div>
                    @endforeach
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 add_new_ip_div">
                        <input class="xgrow-button ip_restriction add_new_ip" type="button" style="width:48px;font-size:30px" value="+">
                    </div>
                    <input type="hidden" id="ip_to_delete" name="ip_to_delete"/>
                </div>
    
                {{-- PERMITIR ALTERAR E-EMAIL --}}
                <div class="row my-2">
                    <div class="col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('allow_changes', true, (old('allow_changes', $config->allow_changes ?? false)), ['id' => 'allow_changes', 'class' => 'form-check-input']) !!}
                            {!! Form::label('allow_changes', 'Permitir alterar e-mail', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <hr class="mt-0" style="border-color: var(--border-color)"/>
            <div class="xgrow-card-body p-3">
                <h5 class="xgrow-card-title my-2" style="font-size: 1.5rem; line-height: inherit">
                    Registros
                </h5>
    
                {{-- LEADS POR ATENDENTE --}}
                <div class="row my-2">
                    <div class="col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('limit_leads', true, (old('limit_leads', $config->limit_leads ?? false)), ['id' => 'limit_leads', 'class' => 'form-check-input']) !!}
                            {!! Form::label('limit_leads', 'Leads por Atendente', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="row my-2">
                    <div class="col-12">
                        <div class="xgrow-form-control mui-textfield mui-textfield--float-label" style="margin-bottom: 16px">
                            {!! Form::select('number_leads', ["5" => 5, "10" => 10, "20" => 20], $config->number_leads ?? 0, ['id' => 'number_leads', 'class' => 'xgrow-select limit_leads']) !!}
                        </div>
                    </div>
                </div>

                {{-- MOTIVOS DE GANHO --}}
                <div class="row my-2">
                    <div class="col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('allow_reasons_gain', true, (old('allow_reasons_gain', $config->allow_reasons_gain ?? false)), ['id' => 'allow_reasons_gain', 'class' => 'form-check-input']) !!}
                            {!! Form::label('allow_reasons_gain', 'Motivos de ganho', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="row my-2 align-items-end reasons_container_gain">
                    @foreach ($reasonsGain as $reason)
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12 reason-gain-element-{{ $reason->id }}">
                            <a class="xgrow-input-delete mb-1 allow_reasons_gain delete_reason_gain" data-reason-gain-id="{{ $reason->id }}" href="javascript:void(0)">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                                {!! Form::text("reasonGain[$reason->id]", $reason->description, ['id' => "reason-gain-$reason->id", 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine allow_reasons_gain']) !!}
                                {!! Form::label("reason-gain-$reason->id", 'Motivo') !!}
                            </div>
                        </div>
                    @endforeach
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 add_new_reason_gain_div">
                        <input class="xgrow-button allow_reasons_gain add_new_reason_gain" type="button" style="width:48px;font-size:30px" value="+">
                    </div>
                    <input type="hidden" id="reason_gain_to_delete" name="reason_gain_to_delete"/>
                </div>
    
                {{-- MOTIVOS DE PERDA --}}
                <div class="row my-2">
                    <div class="col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('allow_reasons_loss', true, (old('allow_reasons_loss', $config->allow_reasons_loss ?? false)), ['id' => 'allow_reasons_loss', 'class' => 'form-check-input']) !!}
                            {!! Form::label('allow_reasons_loss', 'Motivos de perda', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
                <div class="row my-2 align-items-end reasons_container_loss">
                    @foreach ($reasonsLoss as $reason)
                        <div class="col-lg-3 col-md-3 col-sm-6 col-12 reason-loss-element-{{ $reason->id }}">
                            <a class="xgrow-input-delete mb-1 allow_reasons_loss delete_reason_loss" data-reason-loss-id="{{ $reason->id }}" href="javascript:void(0)">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                                {!! Form::text("reasonLoss[$reason->id]", $reason->description, ['id' => "reason-loss-$reason->id", 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine allow_reasons_loss']) !!}
                                {!! Form::label("reason-loss-$reason->id", 'Motivo') !!}
                            </div>
                        </div>
                    @endforeach
                    <div class="col-lg-3 col-md-6 col-sm-12 mb-4 add_new_reason_loss_div">
                        <input class="xgrow-button allow_reasons_loss add_new_reason_loss" type="button" style="width:48px;font-size:30px" value="+">
                    </div>
                    <input type="hidden" id="reason_loss_to_delete" name="reason_loss_to_delete"/>
                </div>
    
                {{-- EMAIL E ENDEREÇO --}}
                <div class="row my-2">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('show_email', true, (old('show_email', $config->show_email ?? false)), ['id' => 'show_email', 'class' => 'form-check-input']) !!}
                            {!! Form::label('show_email', 'Mostrar e-mail para o atendente', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
    
                    <div class="col-lg-3 col-md-3 col-sm-6 col-12 py-2">
                        <div class="form-check form-switch">
                            {!! Form::checkbox('show_address', true, (old('show_address', $config->show_address ?? false)), ['id' => 'show_address', 'class' => 'form-check-input']) !!}
                            {!! Form::label('show_address', 'Mostrar endereço', ['class' => 'form-check-label']) !!}
                        </div>
                    </div>
                </div>
            </div>
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <div class="xgrow-card-footer p-3 border-top">
                <button type="submit" class="xgrow-button">Salvar alterações</button>
            </div>
        </form>
    </div>
@endsection