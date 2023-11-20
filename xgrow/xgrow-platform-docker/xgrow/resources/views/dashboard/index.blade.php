@extends('templates.xgrow.main')

@push('jquery')
@endpush

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/dashboard_index.css') }}" rel="stylesheet">
@endpush

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script>
        const getAPIProducts = @json(route('get.all.products'));
        @if (Session::has('error'))
        errorToast('Erro de acesso', "{{ session('error') }}")
        @endif
    </script>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script>
        const verifyDocument = @json($verifyDocument);
        const recipientStatusMessage = @json($recipientStatusMessage);
    </script>
    <script src="{{ asset('js/bundle/dashboard.js') }}"></script>
@endpush

@section('content')
    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="/">Resumo</a></li>
        </ol>
    </nav>

    <div id="dashboard" class="container-fluid p-0">
        <div class="row">
            <verify-document v-if="verifyDocument" :description="recipientStatusMessage"></verify-document>

        @can('dashboard')
            <div class="col-xxl-9 col-xl-8 col-lg-8 col-md-12 col-sm-12 mb-2">
                {{-- FILTAR POR PERÍODO --}}
                <dashboard-filter :margin-top="0" :products="products">
                    <div id="date-filter-back" @click="dateFilterBack"></div>
                    <div id="date-filter-range">
                        <xgrow-daterange-component v-model:value="dateRange" format="DD/MM/YYYY" :clearable="false"
                            type="date" range placeholder="Selecione o período" style="margin-top: -15px"
                            @change="convertDateTime" />
                    </div>
                    <div id="date-filter-next" @click="dateFilterNext"></div>
                </dashboard-filter>

                {{-- ALUNOS --}}
                <subscriber-component :subscriber-data="subscribers" style="padding: 0 !important"></subscriber-component>

                {{-- GRÁFICO DE VENDAS --}}
                <sales-chart-component :products="products" :chart-data="chartData"
                  @sale-chart-by-product="saleChartByProduct">
                </sales-chart-component>

                {{-- VENDAS --}}
                <card-component title="Vendas" subtitle="Acompanhe em detalhes suas vendas totais e reembolsadas.">
                    <second-card-component :card-data="sales.paid.toString()" icon="fa-dollar-sign" label="Vendas totais"
                        xxl="4" xl="4" lg="4" md="6"></second-card-component>

                    <second-card-component :card-data="sales.refunds.toString()" icon="fa-hand-holding-usd"
                        label="Vendas reembolsadas" xxl="4" xl="4" lg="4" md="6">
                    </second-card-component>

                    <second-card-component :card-data="`${sales.refundPercentage}%`" icon="fa-percentage" label="Reembolso"
                        xxl="4" xl="4" lg="4" md="12"></second-card-component>
                </card-component>

                {{-- CONVERSÃO --}}
                <card-component title="Conversões por forma de pagamento"
                    subtitle="Acompanhe em detalhes suas conversões por forma de pagamento.">
                    <second-card-component :card-data="`${conversion.credit_card.percentage}%`"
                        :info="`${conversion.credit_card.paid}
                            ${conversion.credit_card.paid != 1 ? 'pagas' : 'paga'} de
                            ${conversion.credit_card.generated}
                            ${conversion.credit_card.generated != 1 ? 'vendas geradas' : 'venda gerada'}`"
                        icon="fa-credit-card" label="Cartão de crédito" xxl="4" xl="4" lg="4"
                        md="6">
                    </second-card-component>

                    <second-card-component :card-data="`${conversion.pix.percentage}%`"
                        :info="`${conversion.pix.paid}
                            ${conversion.pix.paid != 1 ? 'pagas' : 'paga'} de
                            ${conversion.pix.generated}
                            ${conversion.pix.generated != 1 ? 'vendas geradas' : 'venda gerada'}`"
                        icon="fa-money-bill" label="PIX" xxl="4" xl="4" lg="4" md="6">
                    </second-card-component>

                    <second-card-component :card-data="`${conversion.boleto.percentage}%`"
                        :info="`${conversion.boleto.paid}
                                                                                                                                ${conversion.boleto.paid != 1 ? 'pagas' : 'paga'} de
                                                                                                                                ${conversion.boleto.generated}
                                                                                                                                ${conversion.boleto.generated != 1 ? 'vendas geradas' : 'venda gerada'}`"
                        icon="fa-file-invoice-dollar" label="Boleto bancário" xxl="4" xl="4" lg="4"
                        md="12">
                    </second-card-component>
                </card-component>
            </div>

            {{-- COLUMN 2 --}}
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-2">
                {{-- DETALHES DO USUÁRIO --}}
                <card-component :margin-top="0">
                    <revenue-component User data :user-name="'{{ Auth::user()->name }}'"
                        :user-email="'{{ Auth::user()->email }}'" :user-img="null" user-link="/user" Goals data
                        :actual-progress="40000" :goal="50000">
                    </revenue-component>
                </card-component>

                {{-- MEUS ALUNOS --}}
                <card-component title="Meus Alunos">
                    <my-students-component :my-students="myStudents"></my-students-component>
                </card-component>

                {{-- CARROUSEL NEWS --}}
                <card-component title="Novidades" subtitle="Veja as novidades da plataforma Xgrow." v-if="false">
                    <carousel-component :carousel-items="carouselItems"></carousel-component>
                </card-component>
            </div>
        @else
            <div class="row">
                <div class="xgrow-container col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <img src="/xgrow-vendor/assets/img/logo/symbol.svg" alt="Logo da xgrow">
                    <h1 class="text-white">Bem vindo à Xgrow!</h1>
                    <p class="text-white">
                        A plataforma completa para entregar seu conhecimento com mais gestão e retenção.
                    </p>
                </div>
            </div>
        @endcan

    </div>
    </div>

    @include('elements.toast')
@endsection
