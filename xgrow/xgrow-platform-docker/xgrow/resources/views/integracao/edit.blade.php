@extends('templates.xgrow.main')

@push('jquery')
    <script src="/xgrow-vendor/plugins/jquery/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
    <script type="text/javascript">
        $(function() {

            $('#prod_seller_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {
                reverse: true
            });
            $('#prod_client_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {
                reverse: true
            });
            $('#prod_secret_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {
                reverse: true
            });
            $('#homol_seller_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {
                reverse: true
            });
            $('#homol_client_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {
                reverse: true
            });
            $('#homol_secret_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {
                reverse: true
            });

        });
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/integracao">Integrações</a></li>
            <li class="breadcrumb-item active mx-2"><span>Editar</span></li>
        </ol>
    </nav>

    <div class="xgrow-card card-dark">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Editar Integração</p>
        </div>
        <form id="create-form" class="mui-form" action="{{route('integracao.update',['id'=>$webhook->id])}}" method="POST">
            <div class="xgrow-card-body">
                @csrf
                @method('PUT')
                <input type="hidden" value="{{ $webhook->id_webhook }}" name="id_webhook">
                <input type="hidden" value="{{ $webhook->id }}" name="id" , id="id">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-3">
                            <input id="url_webhook" autocomplete="off" type="text" spellcheck="false"
                                name="url_webhook" value="{{$webhook->url_webhook ? $webhook->url_webhook : 'https://'}}">
                            <label>Url</label>
                            <span onclick="document.getElementById('url_webhook').value = ''"></span>
                        </div>
                    </div>
                    @include('integracao._form')
                </div>
            </div>
            <div class="xgrow-card-footer">
                <button type="submit" class="xgrow-button">Salvar WebHook</button>
            </div>
        </form>

        @if($webhook->id_integration === 'ACTIVECAMPAIGN')
            <div class="row mx-1 my-4 border-bottom"></div>
            @include('integracao.integration-actions')
        @endif
    </div>
@endsection