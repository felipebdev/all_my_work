@extends('templates.xgrow.main')

@push('after-styles')
    <link rel="stylesheet" href="{{ asset("xgrow-vendor/assets/css/pages/my_account.css") }}">

@endpush

@push('after-scripts')
    <script src="//cdn.muicss.com/mui-0.10.3/js/mui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $('#cpf').mask('000.000.000-00', {
            reverse: true
        }).unbind('change').change(function (e) {
            // validateCpf(e.target)
        });

        $('#valid').mask('00/0000', {
            reverse: true
        }).unbind('change').change(function (e) {
            // validateDate(e.target)
        });

        $('#cvv').mask('0000', {
            reverse: true
        }).unbind('change').change(function (e) {
            // validateCVV(e.target)
        });

        function selectPlan(e) {
            const cards = document.querySelectorAll('.xgrow-card-header');
            const card = document.getElementsByClassName(`check-plan-${e.value}`);
            cards.forEach(cardx => cardx.classList.remove('selected'));
            card[0].classList.add('selected');

            // Get the card info
            const cardTitle = document.getElementsByClassName(`sPlanTitle-${e.value}`);
            const cardValue = document.getElementsByClassName(`sPlanValue-${e.value}`);
            const cardItens = document.getElementsByClassName(`sPlanItem-${e.value}`);

            // Get the exibition card ids
            const cardETitle = document.getElementById('ePlanTitle');
            const cardEValue = document.getElementById('ePlanValue');
            const cardEItens = document.getElementById('ePlanItem');

            // Change the exibition card for plan card selected
            cardETitle.innerHTML = cardTitle[0].innerHTML;
            cardEValue.innerHTML = cardValue[0].innerHTML;
            cardEItens.innerHTML = cardItens[0].innerHTML;
        }
    </script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-5" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Xgrow</a></li>
            <li class="breadcrumb-item active mx-2"><span>Minha conta</span></li>
        </ol>
    </nav>

    <div class="row flex-wrap justify-content-center">
        <div class="xgrow-card card-dark card-price">
            <div class="xgrow-card-header flex-column check-plan-1">
                <div class="xgrow-radio d-flex align-items-center mb-3">
                    <input id="plan_1" name="rdPlanSelect" type="radio" value="1" onclick="selectPlan(this)">
                </div>
                <label for="plan_1" class="xgrow-card-title p-0 sPlanTitle-1">Plano experimental</label>
            </div>
            <hr class="mt-0">
            <div class="xgrow-card-body">
                <ul class="mb-5 plan-item sPlanItem-1" style="padding-left: 0">
                    <li><i class="fa fa-check"></i>Apenas uma plataforma</li>
                    <li><i class="fa fa-check"></i>Até 20 assinantes</li>
                    <li><i class="fa fa-close"></i> Funcionalidades limitadas</li>
                </ul>
                <div class="price">
                    <p>Valor mensal</p>
                    <h3 class="sPlanValue-1">Grátis</h3>
                </div>
            </div>
        </div>

        <div class="xgrow-card card-dark card-price">
            <div class="xgrow-card-header flex-column check-plan-2 selected">
                <div class="xgrow-radio d-flex align-items-center mb-3">
                    <input id="plan_2" name="rdPlanSelect" type="radio" value="2" onclick="selectPlan(this)" checked>
                </div>
                <label for="plan_2" class="xgrow-card-title p-0 sPlanTitle-2">Plano light</label>
            </div>
            <hr class="mt-0">
            <div class="xgrow-card-body">
                <ul class="mb-5 plan-item sPlanItem-2" style="padding-left: 0">
                    <li><i class="fa fa-check"></i>Até 5 plataformas</li>
                    <li><i class="fa fa-check"></i>Até 1000 assinantes</li>
                    <li><i class="fa fa-check"></i>Todas as funcionalidades</li>
                </ul>
                <div class="price">
                    <p>Valor mensal</p>
                    <h3 class="sPlanValue-2">R$ 35,00</h3>
                </div>
            </div>
        </div>

        <div class="xgrow-card card-dark card-price">
            <div class="xgrow-card-header flex-column check-plan-3">
                <div class="xgrow-radio d-flex align-items-center mb-3">
                    <input id="plan_3" name="rdPlanSelect" type="radio" value="3" onclick="selectPlan(this)">
                </div>
                <label for="plan_3" class="xgrow-card-title p-0 sPlanTitle-3">Plano Mega</label>
            </div>
            <hr class="mt-0">
            <div class="xgrow-card-body">
                <ul class="mb-5 plan-item sPlanItem-3" style="padding-left: 0">
                    <li><i class="fa fa-check"></i>20 plataformas</li>
                    <li><i class="fa fa-check"></i>Até 2000 assinantes</li>
                    <li><i class="fa fa-check"></i> Todas as Funcionalidades</li>
                </ul>
                <div class="price">
                    <p>Valor mensal</p>
                    <h3 class="sPlanValue-3">R$ 75,00</h3>
                </div>
            </div>
        </div>

        <div class="xgrow-card card-dark card-price">
            <div class="xgrow-card-header flex-column check-plan-4">
                <div class="xgrow-radio d-flex align-items-center mb-3">
                    <input id="plan_4" name="rdPlanSelect" type="radio" value="4" onclick="selectPlan(this)">
                </div>
                <label for="plan_4" class="xgrow-card-title p-0 sPlanTitle-4">Plano Premium</label>
            </div>
            <hr class="mt-0">
            <div class="xgrow-card-body">
                <ul class="mb-5 plan-item sPlanItem-4" style="padding-left: 0">
                    <li><i class="fa fa-check"></i>Sem limites de plataformas</li>
                    <li><i class="fa fa-check"></i>Sem limites de assinantes</li>
                    <li><i class="fa fa-check"></i> Todas as Funcionalidades</li>
                </ul>
                <div class="price">
                    <p>Valor mensal</p>
                    <h3 class="sPlanValue-4">R$ 125,00</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <hr class="separator">
            </div>
        </div>
        <div class="row" style="width: 80%; margin:0 auto">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <p class="plan-title" id="ePlanTitle">Plano light</p>
                <ul class="mb-3 plan-item" style="padding-left: 0" id="ePlanItem">
                    <li><i class="fa fa-check"></i>Até 5 plataformas</li>
                    <li><i class="fa fa-check"></i>Até 1000 assinantes</li>
                    <li><i class="fa fa-check"></i>Todas as funcionalidades</li>
                </ul>
                <div class="price">
                    <p>Valor mensal</p>
                    <h3 class="price-exibition" id="ePlanValue">R$ 35,00</h3>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="cpf" autocomplete="off" spellcheck="false"
                                   class="mui--is-empty mui--is-untouched mui--is-pristine" required="" name="iptCpf"
                                   type="text">
                            <label for="cpf">CPF do titular do cartão</label>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="name" autocomplete="off" spellcheck="false"
                                   class="mui--is-empty mui--is-untouched mui--is-pristine" required="" name="iptName"
                                   type="text">
                            <label for="name">Nome do titular</label>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="number" autocomplete="off" spellcheck="false"
                                   class="mui--is-empty mui--is-untouched mui--is-pristine" required="" name="iptNumber"
                                   type="text">
                            <label for="number">Número do cartão</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="valid" autocomplete="off" spellcheck="false"
                                   class="mui--is-empty mui--is-untouched mui--is-pristine" required="" name="iptValid"
                                   type="text">
                            <label for="valid">Validade</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input id="cvv" autocomplete="off" spellcheck="false"
                                   class="mui--is-empty mui--is-untouched mui--is-pristine" required="" name="iptCvv"
                                   type="text">
                            <label for="cvv">CVV</label>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-end mt-3">
                        <button type="button" class="xgrow-button">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
