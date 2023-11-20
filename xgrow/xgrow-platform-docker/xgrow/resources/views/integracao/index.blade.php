@extends('templates.xgrow.main')

@push('after-styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/integret_add.css') }}">
@endpush

@push('after-scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.btn-integration-edit').on('click', function (e) {
                const url = $(this).data('url');
                const provider = $(this).data('provider');

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function (data) {
                        integrations[provider](data);
                    },
                    error: function (data) {
                        errorToast('Algum erro aconteceu!',
                            `Veja mais em: ${data.responseJSON.message}`);
                    }
                });
            });

            $('#modal-integration-delete').on('show.bs.modal', function (e) {
                const id = $(e.relatedTarget).data('id');
                $('#frm-modal-delete').attr('action', `/integracao/destroy/${id}`);
            });
        });

        $('.btn-modal').click(function (e) {
            e.preventDefault();
            let id = $(this).attr('data-href');
            let modal = $('#' + id);
            $(modal).addClass('active');

            (function applyModalPropertiesConstraints() {
                $('#fb-all_payment_methods').change(function () {
                    $('#fb-card_payment_methods').prop('checked', false);
                });

                $('#fb-card_payment_methods').change(function () {
                    $('#fb-all_payment_methods').prop('checked', false);
                });
            })();
        });

        $('.btn-close-modal').click(function (e) {
            e.preventDefault();
            $('input[type=text]').not('.ipt-not-hidden').val('').removeClass('mui--is-not-empty');
            $('input[type=text][readonly]').not('.ipt-not-hidden').addClass('d-none');
            $('form').attr('action', '/integracao/store');
            $(this).closest('.modal-integration').removeClass('active');
        });

        $('.modal-integration').click(function (event) {
            if ($(event.target).hasClass('modal-integration')) {
                $('input[type=text]').not('.ipt-not-hidden').val('').removeClass('mui--is-not-empty');
                $('input[type=text][readonly]').not('.ipt-not-hidden').addClass('d-none');
                $('form').attr('action', '/integracao/store');
                $(this).removeClass('active');
            }
        })

        $('.btn-avancar').click(function (e) {
            e.preventDefault();
            buttonsNexPrev(e.currentTarget)
        });

        $('.btn-voltar').click(function (e) {
            e.preventDefault();
            buttonsNexPrev(e.currentTarget);
        });

        function buttonsNexPrev(e) {
            let id = $(e).closest('.modal-integration').attr("id");
            let columnFirst = $("#" + id + " .column-first");
            let columnTwo = $("#" + id + " .column-two");

            if (e.classList.contains('btn-avancar')) {
                $(columnTwo).removeClass('d-none');
                $(columnFirst).addClass('d-none');
            } else {
                $(columnTwo).addClass('d-none');
                $(columnFirst).removeClass('d-none');
            }
        }

    </script>
    <script>
        const integrations = {
            'eduzz': ({
                          id,
                          days_limit_payment_pendent,
                          flag_enable,
                          source_token,
                          trigger_email,
                          url_webhook,
                          name_integration
                      }) => {
                $('#eduzz-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass(
                    'mui--is-not-empty');
                $('#eduzz-flag_enable').prop('checked', Boolean(flag_enable));
                $('#eduzz-source_token').val(source_token).addClass('mui--is-not-empty');
                $('#eduzz-trigger_email').prop('checked', Boolean(trigger_email));
                $('#eduzz-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#eduzz-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
                $('#eduzz-modal form').attr('action', `/integracao/update/${id}`);
            },
            'digitalmanagerguru': ({
                                       id,
                                       days_limit_payment_pendent,
                                       flag_enable,
                                       source_token,
                                       trigger_email,
                                       url_webhook,
                                       name_integration
                                   }) => {
                const sourceToken = JSON.parse(source_token);
                const {
                    api_key = '',
                } = JSON.parse(source_token);
                $('#digitalmanagerguru-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass(
                    'mui--is-not-empty');
                $('#digitalmanagerguru-flag_enable').prop('checked', Boolean(flag_enable));
                $('#digitalmanagerguru-api_key').val(api_key).addClass('mui--is-not-empty');
                $('#digitalmanagerguru-trigger_email').prop('checked', Boolean(trigger_email));
                $('#digitalmanagerguru-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#digitalmanagerguru-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
                $('#digitalmanagerguru-modal form').attr('action', `/integracao/update/${id}`);
            },
            'pandavideo': ({id, flag_enable, source_token, name_integration}) => {
                const {api_key = ''} = JSON.parse(source_token);
                $('#pandavideo-flag_enable').prop('checked', Boolean(flag_enable));
                $('#pandavideo-api_key').val(api_key).addClass('mui--is-not-empty');
                $('#pandavideo-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#pandavideo-modal form').attr('action', `/integracao/update/${id}`);
            },
            'fandone': ({
                            id,
                            days_limit_payment_pendent,
                            flag_enable,
                            source_token,
                            trigger_email,
                            url_webhook,
                            name_integration
                        }) => {
                const sourceToken = JSON.parse(source_token);

                $('#fandone-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass(
                    'mui--is-not-empty');
                $('#fandone-flag_enable').prop('checked', Boolean(flag_enable));
                $('#fandone-source_token').val(source_token).addClass('mui--is-not-empty');
                $('#fandone-trigger_email').prop('checked', Boolean(trigger_email));
                $('#fandone-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#fandone-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
                $('#fandone-prod_count_id').val(sourceToken.production.count_id || '').removeClass('d-none')
                    .addClass('mui--is-not-empty');
                $('#fandone-prod_public_key').val(sourceToken.production.public_key || '').removeClass('d-none')
                    .addClass('mui--is-not-empty');
                $('#fandone-prod_secret_key').val(sourceToken.production.secret_key || '').removeClass('d-none')
                    .addClass('mui--is-not-empty');
                $('#fandone-homol_count_id').val(sourceToken.local.count_id || '').removeClass('d-none').addClass(
                    'mui--is-not-empty');
                $('#fandone-homol_public_key').val(sourceToken.local.public_key || '').removeClass('d-none')
                    .addClass('mui--is-not-empty');
                $('#fandone-homol_secret_key').val(sourceToken.local.secret_key || '').removeClass('d-none')
                    .addClass('mui--is-not-empty');
                $('#fandone-modal form').attr('action', `/integracao/update/${id}`);
            },
            'hotmart': ({
                            id,
                            days_limit_payment_pendent,
                            flag_enable,
                            source_token,
                            trigger_email,
                            url_webhook,
                            name_integration
                        }) => {
                $('#hotmart-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass(
                    'mui--is-not-empty');
                $('#hotmart-flag_enable').prop('checked', Boolean(flag_enable));
                $('#hotmart-source_token').val(source_token).addClass('mui--is-not-empty');
                $('#hotmart-trigger_email').prop('checked', Boolean(trigger_email));
                $('#hotmart-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#hotmart-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
                $('#hotmart-modal form').attr('action', `/integracao/update/${id}`);
            },
            'plx': ({
                        id,
                        days_limit_payment_pendent,
                        flag_enable,
                        source_token,
                        trigger_email,
                        url_webhook,
                        name_integration
                    }) => {
                $('#plx-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass('mui--is-not-empty');
                $('#plx-flag_enable').prop('checked', Boolean(flag_enable));
                $('#plx-source_token').val(source_token).addClass('mui--is-not-empty');
                $('#plx-trigger_email').prop('checked', Boolean(trigger_email));
                $('#plx-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#plx-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
                $('#plx-modal form').attr('action', `/integracao/update/${id}`);
            },
            'activecampaign': ({
                                   id,
                                   flag_enable,
                                   source_token,
                                   url_webhook,
                                   name_integration
                               }) => {
                const sourceToken = JSON.parse(source_token);
                const {
                    api_key = '',
                    on_create_lead: {
                        do_insert_lead: {
                            lead_list_id = '',
                            lead_tags = []
                        } = {}
                    } = {},
                    on_create_subscriber: {
                        do_insert_subscriber: {
                            subscriber_list_id = '',
                            subscriber_tags = [],
                        } = {}
                    } = {}
                } = JSON.parse(source_token);

                $('#activecampaign-flag_enable').prop('checked', Boolean(flag_enable));
                $('#activecampaign-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#activecampaign-url_webhook').val(url_webhook).addClass('mui--is-not-empty');
                $('#activecampaign-api_key').val(api_key).addClass('mui--is-not-empty');
                $('#activecampaign-on_create_lead').prop('checked', Boolean(lead_list_id));
                $('#activecampaign-do_insert_lead').prop('checked', Boolean(lead_list_id));
                $('#activecampaign-ipt_lead_list_id').val(lead_list_id);
                $('#activecampaign-ipt_lead_tags').val(lead_tags);
                $('#activecampaign-on_create_subscriber').prop('checked', Boolean(lead_list_id));
                $('#activecampaign-do_insert_subscriber').prop('checked', Boolean(lead_list_id));
                $('#activecampaign-ipt_subscriber_list_id').val(subscriber_list_id);
                $('#activecampaign-ipt_subscriber_tags').val(subscriber_tags);
                $('#activecampaign-modal form').attr('action', `/integracao/update/${id}`);
            },
            'facebookpixel': ({
                                  id,
                                  days_limit_payment_pendent,
                                  flag_enable,
                                  source_token,
                                  trigger_email,
                                  url_webhook,
                                  name_integration
                              }) => {
                const sourceToken = JSON.parse(source_token);

                $('#fb-flag_enable').prop('checked', Boolean(flag_enable));
                $('#fb-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#fb-pixel_id').val(sourceToken.pixel_id || '').addClass('mui--is-not-empty');
                $('#fb-pixel_token').val(sourceToken.pixel_token || '').addClass('mui--is-not-empty');
                $('#fb-pixel_test_event_code').val(sourceToken.pixel_test_event_code || '').addClass('mui--is-not-empty');
                $('#fb-checkout_visit').prop('checked', Boolean(sourceToken.fb_checkout_visit));
                $('#fb-sales_conversion').prop('checked', Boolean(sourceToken.fb_sales_conversion));
                $('#fb-all_payment_methods').prop('checked', Boolean(sourceToken.fb_all_payment_methods));
                $('#fb-card_payment_methods').prop('checked', Boolean(sourceToken.fb_card_payment_methods));

                // $('#fb-sale_real_price').prop('checked', Boolean(sourceToken.fb_sale_real_price));
                // $('#fb-sale_client_price').prop('checked', Boolean(sourceToken.fb_sale_client_price));
                // $('#fb-sale_real_price').change(function () {
                //     $('#fb-sale_client_price').prop('checked', false);
                //     $('#fb-sale_price_defined').prop('disabled', true);
                // });
                //
                // $('#fb-sale_client_price').change(function () {
                //     $('#fb-sale_real_price').prop('checked', false);
                //     $('#fb-sale_price_defined').prop('disabled', false);
                // });
                //
                // $('#fb-sale_price_defined').maskMoney({
                //     decimal: ',',
                //     thousands: '.',
                //     precision: 2,
                // });
                //
                // if (Boolean(sourceToken.fb_sale_client_price)) {
                //     $('#fb-sale_price_defined').val(sourceToken.fb_sale_price_defined || '');
                // } else {
                //     $('#fb-sale_price_defined').prop( "disabled", true );
                // }

                $('#facebook-modal form').attr('action', `/integracao/update/${id}`);
            },
            'googleads': ({
                              id,
                              days_limit_payment_pendent,
                              flag_enable,
                              source_token,
                              trigger_email,
                              url_webhook,
                              name_integration
                          }) => {
                const sourceToken = JSON.parse(source_token);

                $('#google-flag_enable').prop('checked', Boolean(flag_enable));
                $('#google-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#google-ads_id').val(sourceToken.adwords_id || '').addClass('mui--is-not-empty');
                $('#google-ads_conversion_label').val(sourceToken.ads_conversion_label || '').addClass(
                    'mui--is-not-empty');
                $('#google-ads_checkout_visit').prop('checked', Boolean(sourceToken.ads_checkout_visit));
                $('#google-ads_sales_conversion').prop('checked', Boolean(sourceToken.ads_sales_conversion));
                $('#google-ads_all_payment_methods').prop('checked', Boolean(sourceToken.ads_all_payment_methods));
                $('#google-ads_sale_real_price').prop('checked', Boolean(sourceToken.ads_sale_real_price));
                $('#google-ads_sale_client_price').prop('checked', Boolean(sourceToken.ads_sale_client_price));
                $('#google-modal form').attr('action', `/integracao/update/${id}`);
            },
            'smartnotas': ({
                               id,
                               flag_enable,
                               source_token,
                               url_webhook,
                               name_integration,
                           }) => {
                const sourceToken = JSON.parse(source_token);
                const {
                    process_after_days = '',
                    on_approve_payment: {
                        do_sefaz_doc = false
                    } = {},
                } = JSON.parse(source_token);

                $('#smartnotas-flag_enable').prop('checked', Boolean(flag_enable));
                $('#smartnotas-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#smartnotas-url_webhook').val(url_webhook).addClass('mui--is-not-empty');
                $('#smartnotas-process_after_days').val(process_after_days).addClass('mui--is-not-empty');
                $('#smartnotas-on_approve_payment').prop('checked', Boolean(do_sefaz_doc));
                $('#smartnotas-do_sefaz_doc').prop('checked', Boolean(do_sefaz_doc));
                $('#smartnotas-modal form').attr('action', `/integracao/update/${id}`);
            },
            'wisenotas': ({
                               id,
                               flag_enable,
                               source_token,
                               url_webhook,
                               name_integration,
                           }) => {
                const sourceToken = JSON.parse(source_token);
                const {
                    process_after_days = '',
                    on_approve_payment: {
                        do_sefaz_doc = false
                    } = {},
                } = JSON.parse(source_token);

                $('#wisenotas-flag_enable').prop('checked', Boolean(flag_enable));
                $('#wisenotas-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#wisenotas-url_webhook').val(url_webhook).addClass('mui--is-not-empty');
                $('#wisenotas-process_after_days').val(process_after_days).addClass('mui--is-not-empty');
                $('#wisenotas-on_approve_payment').prop('checked', Boolean(do_sefaz_doc));
                $('#wisenotas-do_sefaz_doc').prop('checked', Boolean(do_sefaz_doc));
                $('#wisenotas-modal form').attr('action', `/integracao/update/${id}`);
            },
            'octadesk': ({
                             id,
                             flag_enable,
                             source_token,
                             name_integration,
                             url_webhook
                         }) => {
                const {
                    api_key = '',
                    email_client = '',
                    on_create_lead: {
                        do_insert_lead = false
                    } = {},
                    on_create_subscriber: {
                        do_insert_client = false
                    } = {}
                } = JSON.parse(source_token);

                $('#octadesk-flag_enable').prop('checked', Boolean(flag_enable));
                $('#octadesk-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#octadesk-api_key').val(api_key || '').addClass('mui--is-not-empty');
                $('#octadesk-email_client').val(email_client || '').addClass('mui--is-not-empty');
                $('#octadesk-on_create_lead').prop('checked', Boolean(do_insert_lead));
                $('#octadesk-do_insert_lead').prop('checked', Boolean(do_insert_lead));
                $('#octadesk-on_create_subscriber').prop('checked', Boolean(do_insert_client));
                $('#octadesk-do_insert_client').prop('checked', Boolean(do_insert_client));
                $('#octadesk-modal form').attr('action', `/integracao/update/${id}`);
            },
            'kajabi': ({
                           id,
                           flag_enable,
                           source_token,
                           name_integration,
                           url_webhook
                       }) => {
                const {
                    email_client = '',
                    on_create_subscriber: {
                        do_access_subscriber = []
                    } = {}
                } = JSON.parse(source_token);

                $('#kajabi-flag_enable').prop('checked', Boolean(flag_enable));
                $('#kajabi-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#kajabi-on_create_subscriber').prop('checked', (do_access_subscriber.length !== 0));
                $('#kajabi-do_access_subscriber').prop('checked', (do_access_subscriber.length !== 0));
                $('#kajabi-email_client').val(email_client).addClass('mui--is-not-empty');
                $('#kajabi-ipt_subscriber_product_list').val(JSON.stringify(do_access_subscriber));
                $('#kajabi-modal form').attr('action', `/integracao/update/${id}`);
            },
            'cademi': ({
                id,
                flag_enable,
                source_token,
                name_integration,
                url_webhook
            }) => {
                const {
                    api_key = '',
                    on_approve_payment: {
                        do_access_subscriber = []
                    } = {}
                } = JSON.parse(source_token);

                $('#cademi-flag_enable').prop('checked', Boolean(flag_enable));
                $('#cademi-name_integration').val(name_integration).addClass('mui--is-not-empty');
                $('#cademi-url_webhook').val(url_webhook).addClass('mui--is-not-empty');
                $('#cademi-api_key').val(api_key).addClass('mui--is-not-empty');
                $('#cademi-on_approve_payment').prop('checked', (do_access_subscriber.length !== 0));
                $('#cademi-do_access_subscriber').prop('checked', (do_access_subscriber.length !== 0));
                $('#cademi-ipt_subscriber_product_list').val(JSON.stringify(do_access_subscriber));
                $('#cademi-modal form').attr('action', `/integracao/update/${id}`);
            },
        };

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Integrações</span></li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs mb-3" id="nav-tab" role="tablist">
        <a class="xgrow-tab-item nav-item nav-link active" id="nav-integration-tab" data-bs-toggle="tab"
           href="#nav-integration" role="tab" aria-controls="nav-resources" aria-selected="true">Integrações</a>
    </div>

    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-integration" role="tabpanel"
             aria-labelledby="nav-integration-tab">
            <div class="xgrow-card card-dark integration-create" style="padding-left: 0;">
                <div class="right">
                    <div class="section-card-integration">
                        <h1>Integrações conectadas</h1>

                        <div style="max-width:400px">
                            @include('elements.alert')
                        </div>
                        <div class="cards-integrates">
                            @if ($webhooks->isNotEmpty())
                                @foreach ($webhooks as $webhook)
                                    @includeIf('integracao.cards.'.strtolower($webhook->id_integration), ['id' =>
                                    $webhook->integration_id, 'status' => $webhook->flag_enable])
                                @endforeach
                            @else
                                <div
                                    class="card-integrate text-center px-0 d-flex justify-content-center align-items-center">
                                    <h4>Sem integrações conectadas</h4>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="section-card-integration">
                        <h1>Outras integrações</h1>
                        <div class="cards-integrates">
                            @includeWhen(!($webhooks->contains('id_webhook', 8)), 'integracao.cards.activecampaign')
                            @includeWhen(!($webhooks->contains('id_webhook', 9)), 'integracao.cards.facebookpixel')
                            @includeWhen(!($webhooks->contains('id_webhook', 10)), 'integracao.cards.googleads')
                            @includeWhen(!($webhooks->contains('id_webhook', 11)), 'integracao.cards.smartnotas')
                            @includeWhen(!($webhooks->contains('id_webhook', 12)), 'integracao.cards.octadesk')
                            @includeWhen(!($webhooks->contains('id_webhook', 13)), 'integracao.cards.digitalmanagerguru')
                            @includeWhen(!($webhooks->contains('id_webhook', 14)), 'integracao.cards.kajabi')
                            @includeWhen(!($webhooks->contains('id_webhook', 15)), 'integracao.cards.cademi')
                            @includeWhen(!($webhooks->contains('id_webhook', 16)), 'integracao.cards.pandavideo')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('integracao.modals.activecampaign')
    @include('integracao.modals.cademi')
    @include('integracao.modals.eduzz')
    @include('integracao.modals.digitalmanagerguru')
    @include('integracao.modals.facebookpixel')
    @include('integracao.modals.fandone')
    @include('integracao.modals.googleads')
    @include('integracao.modals.hotmart')
    @include('integracao.modals.kajabi')
    @include('integracao.modals.octadesk')
    @include('integracao.modals.smartnotas')
    @include('integracao.modals.pandavideo')

    <div class="modal-sections modal fade" id="modal-integration-delete" tabindex="-1"
         aria-labelledby="modal-integration-delete" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="modal-header">
                    <p class="modal-title">Excluir integração</p>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir esta integração?
                </div>
                <div class="modal-footer">
                    <form id="frm-modal-delete" action="{{ url('/integracao/destroy/0') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-success" aria-label="Close">
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

@endsection
