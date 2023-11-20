@push('after-scripts')
    <script>
        function endAttendances() {
            const selects = $('input[name="audiences[]"]');
            let values = selects.filter(":checked").map(function () {
                return this.value;
            }).get();

            if (values.length < 1) {
                errorToast('Atenção!', 'Selecione ao menos um público para encerrar o atendimento.');
                return;
            }

            successToast('Encerrando...', 'Estamos encerrando os atendimentos');

            $.ajax({
                url: "/callcenter/audience/end-attendances",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'audiences[]': values
                },
                success: function(response) {
                    successToast('Sucesso', response.data);
                    window.location.reload();
                },
                error: function(data) {
                    errorToast('Erro', `Houve um erro ao encerrar o atendimento: ${data.responseJSON.message}`);
                }
            });
        }

        function callAudienceModal() {
            const audience = $("#slc-audience-filter option:selected").text();
            const audience_id = $("#slc-audience-filter option:selected").val();

            if (audience_id != null && audience_id != "") {
                $.ajax({
                    url: `/callcenter/audience/get-actions/${audience_id}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.data == null) {
                            $("#sw_change_card").prop('checked', false);
                            $("#sw_resend_boleto").prop('checked', false);
                            $("#sw_resend_access_data").prop('checked', false);
                            $("#ipt_link_pending").val(null);
                            $("#ipt_link_offer").val(null);
                        } else {
                            $("#sw_change_card").prop('checked', response.data.change_card);
                            $("#sw_resend_boleto").prop('checked', response.data.resend_boleto);
                            $("#sw_resend_access_data").prop('checked', response.data.resend_access_data);

                            $("#ipt_link_pending").val(response.data.link_pending);
                            if (response.data.link_pending != null && response.data.link_pending != "") {
                                $("#ipt_link_pending").removeClass("mui--is-empty mui--is-untouched mui--is-pristine");
                                $("#ipt_link_pending").addClass("mui--is-touched mui--is-dirty mui--is-not-empty");
                            }

                            $("#ipt_link_offer").val(response.data.link_offer);
                            if (response.data.link_offer != null && response.data.link_offer != "") {
                                $("#ipt_link_offer").removeClass("mui--is-empty mui--is-untouched mui--is-pristine");
                                $("#ipt_link_offer").addClass("mui--is-touched mui--is-dirty mui--is-not-empty");
                            }
                        }

                        $('#modal-audience-name').text(audience);
                        $('#btn-save-actions').attr('onclick', `saveActions(${audience_id})`);
                        $('#modal-audiences-actions').modal('show');
                    },
                    error: function(data) {
                        errorToast('Erro', `Houve um erro: ${data.responseJSON.message}`);
                    }
                });
            }
        }

        function saveActions(audience) {
            if (audience == null || audience == "") {
                return;
            }

            const sw_change_card = $("#sw_change_card").is(':checked');
            const sw_resend_boleto = $("#sw_resend_boleto").is(':checked');
            const sw_resend_access_data = $("#sw_resend_access_data").is(':checked');
            const ipt_link_pending = $("#ipt_link_pending").val();
            const ipt_link_offer = $("#ipt_link_offer").val();

            $.ajax({
                url: "/callcenter/audience/save-actions",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    'audience': audience,
                    'change_card': sw_change_card,
                    'resend_boleto': sw_resend_boleto,
                    'resend_access_data': sw_resend_access_data,
                    'link_pending': ipt_link_pending,
                    'link_offer': ipt_link_offer,
                },
                success: function(response) {
                    console.log(response);
                    successToast('Sucesso', response.data);
                },
                error: function(data) {
                    errorToast('Erro', `Houve um erro ao salvar as ações: ${data.responseJSON.message}`);
                }
            });

            $('#modal-audiences-actions').modal('hide');
        }
    </script>
@endpush

@push('after-styles')
    <style>
        .buttons-content {
            width: 80%;
            max-width: 100%;
        }

        @media only screen and (max-width: 965px) {
            .buttons-content {
                width: 100%;
            }
        }
    </style>
@endpush

<div class="row">
    <div class="col-sm-12">
        <div class="xgrow-card card-dark my-2 p-0" style="background: transparent;box-shadow: none;">
            <div class="xgrow-card-body d-flex justify-content-between p-0 m-0">
                <div class="xgrow-check d-flex align-items-center">
                    <h5 class="align-self-center">Dados gerais</h5>
                </div>

                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0 ms-0 me-3">
                    <input type="text" value="{{ date('d/m/Y', strtotime(date('Y-m-d').'-1 month')).' - '.date('d/m/Y') }}" class="form-control" name="daterange"
                        id="reportrange" autocomplete="off">
                    <label for="daterange">Filtrar por data</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div class="callcenter-cards">
        <div class="xgrow-card-sm-group w-100 me-2">
            <div class="xgrow-card-sm card-dashboard ms-0 me-1">
                <img src="{{ asset('xgrow-vendor/assets/img/callcenter/lead.svg') }}" alt="Leads">
                <div class="d-flex flex-column">
                    <p class="xgrow-card-sm-data"><span id="cardLeads">-</span></p>
                    <p class="xgrow-card-sm-label">Total de leads</p>
                </div>
            </div>

            <div class="xgrow-card-sm card-dashboard mx-1">
                <img src="{{ asset('xgrow-vendor/assets/img/callcenter/pending.svg') }}" alt="Pending">
                <div class="d-flex flex-column">
                    <p class="xgrow-card-sm-data"><span id="cardPending">-</span></p>
                    <p class="xgrow-card-sm-label">Pendentes</p>
                </div>
            </div>

            <div class="xgrow-card-sm card-dashboard mx-1">
                <img src="{{ asset('xgrow-vendor/assets/img/callcenter/earnings.svg') }}" alt="Gain">
                <div class="d-flex flex-column">
                    <p class="xgrow-card-sm-data"><span id="cardGains">-</span></p>
                    <p class="xgrow-card-sm-label">Ganhos</p>
                </div>
            </div>

            <div class="xgrow-card-sm card-dashboard mx-1">
                <img src="{{ asset('xgrow-vendor/assets/img/callcenter/losses.svg') }}" alt="Losses">
                <div class="d-flex flex-column">
                    <p class="xgrow-card-sm-data"><span id="cardLosses">-</span></p>
                    <p class="xgrow-card-sm-label">Perdas</p>
                </div>
            </div>

            <div class="xgrow-card-sm card-dashboard mx-1">
                <img src="{{ asset('xgrow-vendor/assets/img/callcenter/no-contact.svg') }}" alt="No Contact">
                <div class="d-flex flex-column">
                    <p class="xgrow-card-sm-data"><span id="cardNoContact">-</span></p>
                    <p class="xgrow-card-sm-label">Sem contato</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap my-4 align-items-start justify-content-between">
    <div class="d-flex flex-wrap my-2 align-items-center buttons-content">
        <select class="xgrow-select public-select mb-2 me-1" id="slc-audience-filter">
            <option value="" disabled selected>Público</option>
            @foreach ($audiences_filter as $audience)
                <option value="{{ $audience->id }}">{{ $audience->name }}</option>
            @endforeach
        </select>
    
        <button onclick="callAudienceModal()" class="xgrow-button mb-2 mx-1" style="height:60px">
            Ações
        </button>
    
        <button onclick="window.location.href='/callcenter/attendant/create'" class="xgrow-button mb-2 mx-1" style="height:60px">
            <i class="fa fa-plus"></i> Novo atendente
        </button>
    </div>

    <button onclick="$('#modal-audiences').modal('show')" class="xgrow-button my-2" style="height:60px;background-color:var(--red)">
        Encerrar atendimentos
    </button>
</div>

<div class="modal-sections modal fade" tabindex="-1" id="modal-audiences" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-header">
                <p class="modal-title">Encerrar atendimentos</p>
            </div>
            <div class="modal-body d-flex flex-column">
                <p>Selecione o(s) público(s) para encerrar os atendimentos</p>
                
                <div class="d-flex flex-column mt-4 ms-4 align-self-start align-items-start">
                    @foreach ($audiences_filter as $audience)
                        @if ($audience->callcenter_active !== false)
                            <div class="xgrow-check my-2">
                                {!! Form::checkbox('audiences[]', $audience->id, null, ['id' => 'audience' . $audience->id, 'class' => 'form-check-input']) !!}
                                {!! Form::label('audience' . $audience->id, $audience->name, ['class' => 'form-check-label']) !!}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="endAttendances()">Encerrar</button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
  
<div class="modal-sections modal fade" tabindex="-1" id="modal-audiences-actions" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="d-flex w-100 justify-content-end p-3 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-header">
                <p class="modal-title">Ações</p>
            </div>
            <div class="modal-body d-flex flex-column align-items-stretch">
                <p class="mb-4">Selecione as ações para o público "<span id="modal-audience-name"></span>"</p>

                <div class="d-flex justify-content-center">
                    <div class="form-check form-switch mx-2">
                        <input type="checkbox" name="" id="sw_change_card" class="form-check-input"/>
                        <label for="">Troca de cartão</label>
                    </div>

                    <div class="form-check form-switch mx-2">
                        <input type="checkbox" name="" id="sw_resend_boleto" class="form-check-input"/>
                        <label for="">Reenvio de boleto</label>
                    </div>
                </div>

                <div class="d-flex justify-content-center">
                    <div class="form-check form-switch mx-2">
                        <input type="checkbox" name="" id="sw_resend_access_data" class="form-check-input"/>
                        <label for="">Reenvio de dados de acesso</label>
                    </div>
                </div>

                <div class="d-flex flex-column align-items-stretch mt-4">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <input type="text" class="mui--is-empty mui--is-untouched mui--is-pristine" name="" id="ipt_link_pending" autocomplete="off" spellcheck="false">
                        <label for="" style="text-align: initial;">Link do produto pendente</label>
                    </div>
                </div>

                <div class="d-flex flex-column align-items-stretch mt-2">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mt-2">
                        <input type="text" class="mui--is-empty mui--is-untouched mui--is-pristine" name="" id="ipt_link_offer" autocomplete="off" spellcheck="false">
                        <label for="" style="text-align: initial;">Link do produto oferecido</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-save-actions" type="button" class="btn btn-success" onclick="saveActions()">Salvar</button>
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>