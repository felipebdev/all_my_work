@extends('templates.xgrow.main')

@push('after-styles')
    <link href="{{ asset('xgrow-vendor/assets/css/pages/products.css') }}" rel="stylesheet">
@endpush

@push('jquery')
    <script>
        const _0x204d = ['active', '110nDSmux', '679600OpnIVn', '.tab-content\x20>\x20.show.active', '1eUUMIh', '1jzWtcM', '1336123PqwqPm', 'next', 'prev', '813609vBgVEg', 'addClass', 'click', 'div', 'removeClass', '4989GmOgnS', '1300129fQTiCh', '1289592ErPtPc', '435481mwWHIH', '.previousPage', 'show\x20active'];
        const _0x5da699 = _0x47a2;

        function _0x47a2(_0x11d01f, _0x338e6c) {
            return _0x47a2 = function (_0x204d06, _0x47a264) {
                _0x204d06 = _0x204d06 - 0x1e1;
                let _0x47dbfb = _0x204d[_0x204d06];
                return _0x47dbfb;
            }, _0x47a2(_0x11d01f, _0x338e6c);
        }

        (function (_0x24509b, _0x2c8874) {
            const _0x353366 = _0x47a2;
            while (!![]) {
                try {
                    const _0x201101 = -parseInt(_0x353366(0x1e6)) + parseInt(_0x353366(0x1e3)) + parseInt(_0x353366(0x1e2)) * parseInt(_0x353366(0x1ec)) + -parseInt(_0x353366(0x1ed)) + parseInt(_0x353366(0x1e1)) * parseInt(_0x353366(0x1ee)) + -parseInt(_0x353366(0x1f2)) * -parseInt(_0x353366(0x1eb)) + -parseInt(_0x353366(0x1f3));
                    if (_0x201101 === _0x2c8874) break; else _0x24509b['push'](_0x24509b['shift']());
                } catch (_0x4f842a) {
                    _0x24509b['push'](_0x24509b['shift']());
                }
            }
        }(_0x204d, 0xcc85a), $('.nextPage')[_0x5da699(0x1e8)](function () {
            const _0x5db5de = _0x5da699;
            let _0x5685fe = $('.nav-tabs\x20>\x20.active');
            _0x5685fe[_0x5db5de(0x1ea)](_0x5db5de(0x1f1)), _0x5685fe[_0x5db5de(0x1e4)]('a')[_0x5db5de(0x1e7)](_0x5db5de(0x1f1));
            let _0xfa2e6e = $('.tab-content\x20>\x20.show.active');
            _0xfa2e6e['removeClass'](_0x5db5de(0x1f0)), _0xfa2e6e[_0x5db5de(0x1e4)](_0x5db5de(0x1e9))[_0x5db5de(0x1e7)]('show\x20active');
        }), $(_0x5da699(0x1ef))[_0x5da699(0x1e8)](function () {
            const _0x484cc2 = _0x5da699;
            let _0x5e9e1d = $('.nav-tabs\x20>\x20.active');
            _0x5e9e1d['removeClass'](_0x484cc2(0x1f1)), _0x5e9e1d[_0x484cc2(0x1e5)]('a')[_0x484cc2(0x1e7)](_0x484cc2(0x1f1));
            let _0x4dba1b = $(_0x484cc2(0x1f4));
            _0x4dba1b[_0x484cc2(0x1ea)](_0x484cc2(0x1f0)), _0x4dba1b[_0x484cc2(0x1e5)](_0x484cc2(0x1e9))['addClass'](_0x484cc2(0x1f0));
        }));
    </script>
@endpush

@section('content')

    <nav class="xgrow-breadcrumb my-3 mb-0" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Início</a></li>
            <li class="breadcrumb-item active mx-2"><a href="/products">Produtos</a></li>
        </ol>
    </nav>

    <div class="xgrow-tabs nav nav-tabs" id="nav-tab" role="tablist">
        @if(!isset($product->id))
            <a class="xgrow-tab-item nav-item nav-link {{ (!Request::get('plan') && !Request::get('type')) ? 'active' : '' }}"
               id="product-type-tab" href="javascript:void(0)" role="tab" aria-controls="product-type"
               aria-selected="true">Tipo de
                produto</a>

            <a class="xgrow-tab-item nav-item nav-link {{ Request::get('type') ? 'show active' : '' }}"
               id="informations-tab" href="javascript:void(0)" role="tab" aria-controls="informations"
               aria-selected="false">Informações</a>
        @else
            <a class="xgrow-tab-item nav-item nav-link {{ Route::current()->getName() === 'products.plan' ? 'active' : '' }}"
               id="sales-plan-tab" href="javascript:void(0)" role="tab" aria-controls="sales-plan"
               aria-selected="false">Plano de venda</a>

            @if(!Request::get('new'))
                <a class="xgrow-tab-item nav-item nav-link {{ Route::current()->getName() === 'products.delivery' ? 'active' : '' }}"
                   id="delivery-tab" href="javascript:void(0)" role="tab" aria-controls="delivery"
                   aria-selected="false">Entrega</a>

                <a class="xgrow-tab-item nav-item nav-link {{ Route::current()->getName() === 'products.info' ? 'active' : '' }}"
                   id="last-flow-tab" href="javascript:void(0)" role="tab" aria-controls="last-flow"
                   aria-selected="false">Apresentação</a>
            @endif
        @endif
    </div>

    <div class="tab-content" id="nav-tabContent">
        @include('elements.alert')
        @if(!isset($product->id))
            @include('products._tab-product-type')
            @include('products._tab-informations')
        @else
            @if(Route::current()->getName() === 'products.plan')
                @include('products._tab-sales-plan')
            @elseif(Route::current()->getName() === 'products.delivery')
                @include('products._tab-delivery')
            @elseif(Route::current()->getName() === 'products.info')
                @include('products._tab-product-last-flow')
            @endif
        @endif
    </div>
    @include('elements.toast')
    @include('elements.confirmation-modal')
    {{--    @include('up_image.modal-xgrow', ['restrictAcceptedFormats' => 'image/*'])--}}
@endsection
