<div id="modal-infusion" class="modal-integration modal-integration-two-items">
    <div class="modal-integration-wrapper">
        <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
        <div class="integration-info">
            <img width="230" src="{{ asset('xgrow-vendor/assets/img/infusion.png') }}" alt="">
            <p>InfusionSoft combina CRM, marketing e automação de vendas e pagamentos em uma plataforma. O resultado? Mais vendas. Menos tarde da noite.</p>
            <a href="https://keap.com/?h=hv" target="_blank">Saber mais sobre</a>
        </div>
        @php 
            $appKey = config('apps.services.infusion.app_key');
            $redirect = route('apps.integrations.oauth.callback');
        @endphp 
        <form action="https://accounts.infusionsoft.com/app/oauth/authorize" method="GET">
            <input type="hidden" name="client_id" value="{{ $appKey }}">
            <input type="hidden" name="redirect_uri" value="{{ $redirect }}">
            <input type="hidden" name="response_type" value="code">
            <input type="hidden" name="scope" value="full">
            <input type="hidden" name="state" value="{{ base64_encode(Auth::user()->platform_id.'#'.\Modules\Integration\Enums\TypeEnum::INFUSION) }}">

            <button class="xgrow-button my-2">Login infusion</button>
            <div class="footer-modal p-0 my-4">
                <button type="button" class="xgrow-button-cancel btn-close-modal ">Cancelar</button>
            </div>
        </form>
    </div>
</div>