@extends('templates.xgrow.main')


@push('jquery')
    <link rel="stylesheet" href="{{ asset('xgrow-vendor/assets/css/pages/integret_add.css') }}">
    <script src="/xgrow-vendor/plugins/jquery/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.12/jquery.mask.min.js"></script>
    <script type="text/javascript">
        $(function () {
            $('#prod_seller_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {reverse: true});
            $('#prod_client_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {reverse: true});
            $('#prod_secret_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {reverse: true});
            $('#homol_seller_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {reverse: true});
            $('#homol_client_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {reverse: true});
            $('#homol_secret_id').mask('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', {reverse: true});

            $("#getnetData").hide();

            $("#provedor").change(function () {
                $("#provedor option:selected").each(function() {
                    let getnet = ($(this).val() == 4)  ? true : false;

                    if (getnet) {
                        $("#getnetData").show();
                        $("#token").attr('required', false);
                    } else {
                        $("#getnetData").hide();
                        $("#token").attr('required', true);
                    }

                    $("#prod_seller_id").attr('required', getnet);
                    $("#prod_client_id").attr('required', getnet);
                    $("#prod_secret_id").attr('required', getnet);

                    $("#homol_seller_id").attr('required', getnet);
                    $("#homol_client_id").attr('required', getnet);
                    $("#homol_secret_id").attr('required', getnet);

                    let mundipagg = ($(this).val() == 5)  ? true : false;

                    if (mundipagg) {
                        $("#mundipaggData").show();
                        $("#token").attr('required', false);
                    } else {
                        $("#mundipaggData").hide();
                        $("#token").attr('required', true);
                    }

                    $("#prod_count_id").attr('required', mundipagg);
                    $("#prod_public_key").attr('required', mundipagg);
                    $("#prod_secret_key").attr('required', mundipagg);

                    $("#homol_count_id").attr('required', mundipagg);
                    $("#homol_public_key").attr('required', mundipagg);
                    $("#homol_secret_key").attr('required', mundipagg);

                    console.log('getnet: ' + getnet);
                    console.log('mundipagg: ' + mundipagg);
                });
            });
        });

        $('.btn-modal').click(function(e) {
            e.preventDefault();
            let id = $(this).attr('data-href');
            let modal = $('#' + id);
            $('html, body').css('overflowY', 'hidden'); 
            $(modal).addClass('active');
        });

        $('.btn-close-modal').click(function(e) {
            e.preventDefault();
            $('html, body').css('overflowY', 'auto'); 
            $(this).closest('.modal-integration').removeClass('active');
        });

        $('.modal-integration').click(function() {
            if($(event.target).hasClass('modal-integration')) {
                $(this).removeClass('active');
            }
        })

        $('.btn-avancar').click(function(e) {
            e.preventDefault();
            buttonsNexPrev(e.currentTarget)
        });

        $('.btn-voltar').click(function(e) {
            e.preventDefault();
            buttonsNexPrev(e.currentTarget);
        });

        function buttonsNexPrev(e) {
            let id = $(e).closest('.modal-integration').attr("id");
            let columnFirst = $("#" + id + " .column-first");
            let columnTwo = $("#" + id + " .column-two");

            if(e.classList.contains('btn-avancar')) {
                $(columnTwo).removeClass('d-none');
                $(columnFirst).addClass('d-none');
            } else {
                $(columnTwo).addClass('d-none');
                $(columnFirst).removeClass('d-none');
            }
        }

    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb mt-3" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item"><a href="/platform-config">Configurações</a></li>
            <li class="breadcrumb-item"><a href="/integracao">Integrações</a></li>
            <li class="breadcrumb-item active mx-2"><span>Novo</span></li>
        </ol>
    </nav>

    {{-- <div class="xgrow-card card-dark">
        <div class="xgrow-card-header">
            <p class="xgrow-card-title">Criar Integração</p>
        </div>
        <form id="create-form" class="mui-form" action="{{url('/integracao/store')}}" method="POST">
            <div class="xgrow-card-body">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="xgrow-form-control xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <select class="xgrow-select" id="provedor" name="id_integration" required>
                                <option value="option1" disabled selected hidden></option>
                                @foreach($providers as $key => $val)
                                    @if($val['status'] === 1)
                                        <option value="{{ $key }}">{{ $val['name'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                            {!! Form::label('id_integration', 'Provedor') !!}
                        </div>
                    </div>
                    @include('integracao._form')
                </div>
            </div>
            <div class="xgrow-card-footer">
                <button type="submit" class="xgrow-button">Criar WebHook</button>
            </div>
        </form>
    </div> --}}

    {{-- LEIA ISSO AQUI --}}
    {{-- Os botões de editar e "Conectar" devem ter no data-href o id do modal --}}

    <div class="xgrow-card card-dark integration-create" style="padding-left: 0;">
        <div class="left">
            <ol>
                <li><a href="#" class="active">Todos</a> </li>
                {{-- <li><a href="#">Pixel</a> </li>
                <li><a href="#">E-mail marketing</a> </li> --}}
            </ol>
        </div>

        <div class="right">
            <div class="section-card-integration">
                <h1>Integrações conectadas</h1>
                {{-- <div class="cards-integrates">
                    <div class="card-integrate">
                        <div class="left-card">
                            <div class="title-card">
                                <img src="{{ asset('xgrow-vendor/assets/img/integration-img.png') }}" alt="">
                                <h2>Facebook Pixel</h2>
                            </div>
                            <p>Texto relacionado as funcionalidades do Facebook Pixel</p>
                        </div>
                        <div class="right-card">
                            <div class="top-card">
                                <i class="fas fa-check-square"></i>
                                <span>Conectado</span>
                            </div>
                            <div class="bottom-card">
                                <a href="#" class="btn-modal" data-href="facebook-modal"><i class="fas fa-edit"></i></a>
                                <a href="#"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-integrate">
                        <div class="left-card">
                            <div class="title-card">
                                <img src="{{ asset('xgrow-vendor/assets/img/integration-img.png') }}" alt="">
                                <h2>Google ADS</h2>
                            </div>
                            <p>Texto relacionado as funcionalidades do Google ADS</p>
                        </div>
                        <div class="right-card">
                            <div class="top-card">
                                <i class="fas fa-check-square"></i>
                                <span>Conectado</span>
                            </div>
                            <div class="bottom-card">
                                <a href="#" class="btn-modal" data-href="google-modal"><i class="fas fa-edit"></i></a>
                                <a href="#"><i class="fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div> --}}
                
            </div>

            <div class="section-card-integration">
                <h1>Outras integrações</h1>
                <div class="cards-integrates">
                    <div class="card-integrate connect">
                        <div class="left-card">
                            <div class="title-card">
                                <img src="{{ asset('xgrow-vendor/assets/img/integration-img.png') }}" alt="">
                                <h2>ActiveCampaign</h2>
                            </div>
                            <p>Texto relacionado as funcionalidades do ActiveCampaign</p>
                        </div>
                        <div class="right-card">
                            <div class="top-card">
                                <a class="btn-conectar btn-modal" data-href="active-campaign-modal">Conectar</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-integrate connect">
                        <div class="left-card">
                            <div class="title-card">
                                <img src="{{ asset('xgrow-vendor/assets/img/integration-img.png') }}" alt="">
                                <h2>Facebook Pixel</h2>
                            </div>
                            <p>Texto relacionado as funcionalidades do Facebook Pixel</p>
                        </div>
                        <div class="right-card">
                            <div class="top-card">
                                <a class="btn-conectar btn-modal" data-href="facebook-modal">Conectar</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-integrate connect">
                        <div class="left-card">
                            <div class="title-card">
                                <img src="{{ asset('xgrow-vendor/assets/img/integration-img.png') }}" alt="">
                                <h2>Google Pixel</h2>
                            </div>
                            <p>Texto relacionado as funcionalidades do Google Pixel</p>
                        </div>
                        <div class="right-card">
                            <div class="top-card">
                                <a class="btn-conectar btn-modal" data-href="google-modal">Conectar</a>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>

            <div class="xgrow-card-footer">
                <button type="submit" class="xgrow-button">Salvar alterações</button>
            </div>
            
        </div>
        
    </div>

    <div id="active-campaign-modal" class="modal-integration">
        <div class="modal-integration-wrapper">
            <a class="icon-close-modal btn-close-modal "><i class="fas fa-times"></i></a>
            <div class="top-modal">
                <img src="{{ asset('xgrow-vendor/assets/img/active-campaign.png') }}" alt="">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisl eu sit feugiat magna odio. Nibh ante sit tellus ipsum ac penatibus vulputate. Odio nulla eget tortor vel. Nisl id elementum purus nisl vestibulum nisl aliquet aenean eget.</p>
                <a href="">Saber mais sobre</a>
            </div>

            <div class="input-nome-conta">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input required="" id="nome-conta" name="nome-conta" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="nome-conta">Nome da conta</label>
                </div>

            </div>

            <div class="input-url-key">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input required="" id="url-api" name="url-api" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="url-api">URL da API</label>
                </div>
                <a href="">Como posso conseguir a URL da API?</a>

                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input required="" id="key-api" name="key-api" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                    <label for="key-api">Key da API</label>
                </div>
                <a href="">Como posso conseguir a Key da API?</a>
            </div>

            <div class="footer-modal">
                <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                <button type="submit" class="xgrow-button">Integrar</button>
            </div>
        </div>
    </div>

    <div id="facebook-modal" class="modal-integration modal-integration-two-items">
        <div class="modal-integration-wrapper">
            <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
            <div class="top-modal">
                <img src="{{ asset('xgrow-vendor/assets/img/facebook-pixel.png') }}" alt="">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisl eu sit feugiat magna odio. Nibh ante sit tellus ipsum ac penatibus vulputate. Odio nulla eget tortor vel. Nisl id elementum purus nisl vestibulum nisl aliquet aenean eget.</p>
                <a href="#">Saber mais sobre</a>
            </div>

            <form class="column-first ">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input required="" id="id-pixel-facebook" name="id-pixel-facebook" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="id-pixel-facebook">ID do pixel Facebook</label>
                    </div>
                </div>
    
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button btn-avancar">Avançar</button>
                </div>
            </form>

            <form class="column-two d-none">
                <div class="top-column-two">
                    <label for="">Quais informações deseja receber?</label>

                    <div class="checkbox-modal">
                        <input id="fb-check-visitas" type="checkbox">
                        <label for="fb-check-visitas" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="fb-check-visitas">Visitas em checkout</label>
                            <label for="fb-check-visitas">Você saberá quantas pessoas visitaram a página de pagamento</label>
                        </div>
                    </div>

                    <div class="checkbox-modal">
                        <input id="fb-check-conversao" type="checkbox">
                        <label for="fb-check-conversao" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="fb-check-conversao">Conversão de vendas</label>
                            <label for="fb-check-conversao">Você saberá quantas pessoas chegaram até a “página de obrigado” do produto </label>
                        </div>
                    </div>
                </div>

                <div class="middle-column-two">
                    <p>Avançado</p>

                    <div class="confirms">
                        <div class="receber">
                            <p>Receber confirmação de venda de qual meio de pagamento?</p>
                            <div class="radio-confirmacao">
                                <input id="fb-radio-todos-meio" name="fb-radio-pagamento" type="radio">
                                <label for="fb-radio-todos-meio" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="fb-radio-todos-meio">Todos os meios de pagamento</label>
                                </div>
                            </div>
                            <div class="radio-confirmacao">
                                <input id="fb-radio-cartao" name="fb-radio-pagamento" type="radio">
                                <label for="fb-radio-cartao" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="fb-radio-cartao">Somente cartão de crédito</label>
                                </div>
                            </div>
                        </div>

                        <div class="receber mt-3">
                            <p>Receber confirmação com qual valor?</p>
                            <div class="radio-confirmacao">
                                <input id="fb-radio-valor-venda" name="fb-radio-venda" type="radio">
                                <label for="fb-radio-valor-venda" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="fb-radio-valor-venda">Valor real da venda</label>
                                </div>
                            </div>
                            <div class="radio-confirmacao">
                                <input id="fb-radio-valor-definir" name="fb-radio-venda" type="radio">
                                <label for="fb-radio-valor-definir" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="fb-radio-valor-definir">Valor que eu definir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-voltar">Voltar</button>
                    <button type="submit" class="xgrow-button">Integrar</button>
                </div>
            </form>
            
        </div>
    </div>

    <div id="google-modal" class="modal-integration modal-integration-two-items">
        <div class="modal-integration-wrapper">
            <a class="icon-close-modal btn-close-modal"><i class="fas fa-times"></i></a>
            <div class="top-modal">
                <img src="{{ asset('xgrow-vendor/assets/img/google-pixel.png') }}" alt="">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nisl eu sit feugiat magna odio. Nibh ante sit tellus ipsum ac penatibus vulputate. Odio nulla eget tortor vel. Nisl id elementum purus nisl vestibulum nisl aliquet aenean eget.</p>
                <a href="#">Saber mais sobre</a>
            </div>

            <form class="column-first">
                <div class="input-two-first">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-4">
                        <input required="" id="id-pixel-google" name="id-pixel-google" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="id-pixel-google">ID do pixel Adwords</label>
                    </div>
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        <input required="" id="id-conversao-google" name="id-conversao-google" type="text" class="mui--is-empty mui--is-untouched mui--is-pristine">
                        <label for="id-conversao-google">Label de conversão do Adwords</label>
                    </div>
                </div>
    
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-close-modal">Cancelar</button>
                    <button type="submit" class="xgrow-button btn-avancar">Avançar</button>
                </div>
            </form>

            <form class="column-two d-none">
                <div class="top-column-two">
                    <label for="">Quais informações deseja receber?</label>

                    <div class="checkbox-modal">
                        <input id="gg-check-visitas" type="checkbox">
                        <label for="gg-check-visitas" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="gg-check-visitas">Visitas em checkout</label>
                            <label for="gg-check-visitas">Você saberá quantas pessoas visitaram a página de pagamento</label>
                        </div>
                    </div>

                    <div class="checkbox-modal">
                        <input id="gg-check-conversao" type="checkbox">
                        <label for="gg-check-conversao" class="check-input-label"></label>
                        <div class="label-right-check">
                            <label for="gg-check-conversao">Conversão de vendas</label>
                            <label for="gg-check-conversao">Você saberá quantas pessoas chegaram até a “página de obrigado” do produto </label>
                        </div>
                    </div>
                </div>

                <div class="middle-column-two">
                    <p>Avançado</p>

                    <div class="confirms">
                        <div class="receber">
                            <p>Receber confirmação de venda de qual meio de pagamento?</p>
                            <div class="radio-confirmacao">
                                <input id="gg-radio-todos-meio" name="gg-radio-pagamento" type="radio">
                                <label for="gg-radio-todos-meio" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="gg-radio-todos-meio">Todos os meios de pagamento</label>
                                </div>
                            </div>
                            <div class="radio-confirmacao">
                                <input id="gg-radio-cartao" name="gg-radio-pagamento" type="radio">
                                <label for="gg-radio-cartao" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="gg-radio-cartao">Somente cartão de crédito</label>
                                </div>
                            </div>
                        </div>
                        <div class="receber mt-3">
                            <p>Receber confirmação com qual valor?</p>
                            <div class="radio-confirmacao">
                                <input id="gg-radio-valor-venda" name="gg-radio-valor" type="radio">
                                <label for="gg-radio-valor-venda" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="gg-radio-valor-venda">Valor real da venda</label>
                                </div>
                            </div>
                            <div class="radio-confirmacao">
                                <input id="gg-radio-valor-definir" name="gg-radio-valor" type="radio">
                                <label for="gg-radio-valor-definir" class="radio-input-label"></label>
                                <div class="label-right-radio">
                                    <label for="gg-radio-valor-definir">Valor que eu definir</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                <div class="footer-modal">
                    <button class="xgrow-button-cancel btn-voltar">Voltar</button>
                    <button type="submit" class="xgrow-button">Integrar</button>
                </div>
            </form>
            
        </div>
    </div>
@endsection
