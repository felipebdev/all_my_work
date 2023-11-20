@push('after-scripts')
    <script>
        $(document).ready(function () {
            $(".integration-card.to-connect").on('click', function () {
                const integration = $(this).data("integration-name");

                if (integration) {
                    $('#integrations-list').removeClass('active');
                    $(`#modal-${integration}`).addClass("active");
                }
            });

            $('.btn-avancar').click(function (e) {
                e.preventDefault();

                // Chamar temporariamente o modal de contas conectadas
                const integration = $(this).data('integration-name');
                const page = $(this).attr('data-page') ?? null;

                // Change page on Facebook and Google cards
                if (
                    (integration == "facebook" || integration == "google") &&
                    page == 1
                ) {
                    $(`.${integration}-part-1`).addClass('d-none');
                    $(`.${integration}-part-2`).removeClass('d-none');
                    $(this).attr('data-page', 2);
                    return;
                }

                $(this).closest('.modal-integration').removeClass('active');
                $("#action-caller").attr('data-integration-name', integration);

                if (integration != 'activecampaign' && integration != 'infusion') {
                    $("#action-caller").text('Editar ação');
                } else {
                    $("#action-caller").text('Visualizar ações');
                }

                $('#account-connected-list').addClass('active');

                // buttonsNexPrev(e.currentTarget)
            });

            $('.btn-voltar').click(function (e) {
                e.preventDefault();
                buttonsNexPrev(e.currentTarget);
            });

            $("#action-caller").click(function (e) {
                const integration = $(this).data('integration-name');

                if (integration != 'activecampaign' && integration != 'infusion') {
                    $(this).closest('.modal-integration').removeClass('active');
                    $(`#modal-${integration}-action`).addClass('active');
                } else {
                    window.location.href = '/apps/integrations/id/actions';
                }
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
        });
    </script>
@endpush

<div id="integrations-list" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="top-modal">
            <h5>Integrações disponíveis</h3>
            {{-- <div class="modal-search-ipt">
                <input type="text" placeholder="Busca por nome">
                <a href="#"><i class="fas fa-search"></i></a>
            </div> --}}
        </div>

        <div class="integrations-list">
            {{-- ActiveCampaign --}}
            <div class="integration-card to-connect" data-integration-name="activecampaign">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/activecampaign-icon.png') }}">
                        <p>ActiveCampaign</p>
                    </div>
                    <div class="card-desc">
                        <p>Automatize seu marketing em apenas alguns cliques.</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Cademí --}}
            <div class="integration-card to-connect" data-integration-name="cademi">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/cademi-icon.png') }}">
                        <p>Cademí</p>
                    </div>
                    <div class="card-desc">
                        <p>Experiência de aprendizagem moderna e intuitiva com a plataforma Cademí</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Digital Manager Guru --}}
            <div class="integration-card to-connect" data-integration-name="digitalmanagerguru">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/digitalmanagerguru-icon.png') }}">
                        <p>Digital Manager Guru</p>
                    </div>
                    <div class="card-desc">
                        <p>A forma mais simples e poderosa de vender online</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Facebook Pixel --}}
            <div class="integration-card to-connect" data-integration-name="facebookpixel">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/facebook-pixel-icon.png') }}">
                        <p>Facebook Pixel</p>
                    </div>
                    <div class="card-desc">
                        <p>Ferramenta analítica para ajudar a mensurar o sucesso de uma campanha</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Google Ads --}}
            <div class="integration-card to-connect" data-integration-name="googleads">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/google-pixel-icon.png') }}">
                        <p>Google Ads</p>
                    </div>
                    <div class="card-desc">
                        <p>Google Ads é o principal serviço de publicidade da Google</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Infusion --}}
            <div class="integration-card to-connect" data-integration-name="infusion">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/infusion-icon.png') }}">
                        <p>InfusionSoft</p>
                    </div>
                    <div class="card-desc">
                        <p>Automação de vendas e marketing desenvolvida para ajudá-lo a crescer sem caos</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Kajabi --}}
            <div class="integration-card to-connect" data-integration-name="kajabi">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/kajabi-icon.png') }}">
                        <p>Kajabi</p>
                    </div>
                    <div class="card-desc">
                        <p>Crie e venda online cursos e treinamentos com Kajabi</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Mailchimp --}}
            <div class="integration-card to-connect" data-integration-name="mailchimp">
                <div class="card-left">
                    <div class="card-title">
                        <img class="rounded" src="{{ asset('xgrow-vendor/assets/img/mailchimp-icon.jpg') }}">
                        <p>Mailchimp</p>
                    </div>
                    <div class="card-desc">
                        <p>Mailchimp é uma plataforma automação de marketing e serviço de email marketing</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Octadesk --}}
            <div class="integration-card to-connect" data-integration-name="octadesk">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/octadesk-icon.png') }}">
                        <p>Octadesk</p>
                    </div>
                    <div class="card-desc">
                        <p>Sistema de Marketing, Vendas e Atendimento</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Panda Video --}}
            <div class="integration-card to-connect" data-integration-name="pandavideo">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/panda-video-icon.png') }}">
                        <p>Panda Video</p>
                    </div>
                    <div class="card-desc">
                        <p>Segurança em hospedagem de vídeos para marketing digital e cursos online</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- SmartNotas --}}
            <div class="integration-card to-connect" data-integration-name="smartnotas">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/smartnotas-icon.png') }}">
                        <p>SmartNotas</p>
                    </div>
                    <div class="card-desc">
                        <p>SmartNotas é sistema de emissão de notas fiscais inteligente.</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>

            {{-- Webhook --}}
            <div class="integration-card to-connect" data-integration-name="webhook">
                <div class="card-left">
                    <div class="card-title">
                        <img src="{{ asset('xgrow-vendor/assets/img/webhook-icon.png') }}">
                        <p>Webhook</p>
                    </div>
                    <div class="card-desc">
                        <p>Webhook é a forma de receber informações entre a Xgrow e outro sistema.</p>
                    </div>
                </div>
                <div class="card-right">
                    <div class="info-label">
                        <p>Conectar</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>