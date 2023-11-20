@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/integret_add.css') }}">
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/new-integrations.css') }}">

    <style>
        .x-dropdown {
            position: unset !important;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.1.2/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>

    @include('apps::integrations.includes.scripts')
    <script>
        $(document).ready(function() {
            $(".new-integration").on('click', function() {
                $("#integrations-list").addClass('active');
            });

            $('.btn-close-modal').click(function(e) {
                e.preventDefault();
                $('input[type=text]').not('.ipt-not-hidden').val('').removeClass('mui--is-not-empty');
                $('input[type=text][readonly]').not('.ipt-not-hidden').addClass('d-none');
                const isQueueableModal = $(this).closest('.modal-integration').find('input[name=type]')
                    .length > 0;
                const form = $(this).closest('.modal-integration').find('form');
                if (isQueueableModal) {
                    form.attr('action', '/apps/integrations');
                } else {
                    form.attr('action', '/integracao/store');
                }
                form.find('input[name=_method]').remove();
                $(this).closest('.modal-integration').removeClass('active');

                $('.facebook-part-1').removeClass('d-none');
                $('.facebook-part-2').addClass('d-none');
                $('.google-part-1').removeClass('d-none');
                $('.google-part-2').addClass('d-none');
            });

            $('.btn-modal').click(function(e) {
                e.preventDefault();
                let id = $(this).attr('data-href');
                let modal = $('#' + id);
                $(modal).addClass('active');

                (function applyModalPropertiesConstraints() {
                    $('#fb-all_payment_methods').change(function() {
                        $('#fb-card_payment_methods').prop('checked', false);
                    });

                    $('#fb-card_payment_methods').change(function() {
                        $('#fb-all_payment_methods').prop('checked', false);
                    });
                })();
            });


            $('.btn-integration-edit').on('click', function(e) {
                const url = $(this).data('url');
                const provider = $(this).data('provider');

                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        integrations[provider](data);
                    },
                    error: function(data) {
                        errorToast('Algum erro aconteceu!',
                            `Veja mais em: ${data.responseJSON.message}`);
                    }
                });
            });

            $('#modal-integration-delete').on('show.bs.modal', function(e) {
                const url = $(e.relatedTarget).data('url');
                $('#frm-modal-delete').attr('action', url);
            });
        });
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><span>Integrações</span></li>
        </ol>
    </nav>
    <div class="xgrow-card card-dark">
        <div class="xgrow-card-header align-items-center justify-content-between flex-wrap">
            <h5>Integrações</h5>

            <button class="xgrow-button border-light new-integration">
                <i class="fa fa-plus" aria-hidden="true"></i> Nova integração
            </button>
        </div>

        @include('elements.alert')
        <div class="xgrow-card-body">
            @if (!empty($apps))
                <div class="row">
                    @foreach ($apps as $app)
                        @include('apps::integrations.cards.' . strtolower($app->type), ['app' => $app])
                    @endforeach
                </div>
            @else
                <div class="card-integrate py-4 d-flex">
                    <h4>Sem integrações conectadas</h4>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Include --}}
    @include('apps::integrations.modals.activecampaign')
    @include('apps::integrations.modals.cademi')
    @include('apps::integrations.modals.digitalmanagerguru')
    @include('apps::integrations.modals.eduzz')
    @include('apps::integrations.modals.facebookpixel')
    @include('apps::integrations.modals.googleads')
    @include('apps::integrations.modals.hotmart')
    @include('apps::integrations.modals.hubspot')
    @include('apps::integrations.modals.infusion')
    @include('apps::integrations.modals.kajabi')
    @include('apps::integrations.modals.mailchimp')
    @include('apps::integrations.modals.octadesk')
    @include('apps::integrations.modals.pandavideo')
    @include('apps::integrations.modals.rdstation')
    @include('apps::integrations.modals.smartnotas')
    @include('apps::integrations.modals.wisenotas')
    @include('apps::integrations.modals.webhook')
    @include('apps::integrations.modals.pipedrive')
    @include('apps::integrations.modals.leadlovers')
    @include('apps::integrations.modals.mautic')
    @include('apps::integrations.modals.voxuy')
    @include('apps::integrations.modals.enotas')
    @include('apps::integrations.modals.memberkit')
    @include('apps::integrations.modals.tiktok')
    @include('apps::integrations.modals.builderall')
    @include('apps::integrations.modals.notazz')

    @include('apps::integrations.modals.integrations-list')
    @include('apps::integrations.includes.delete')
@endsection
