@extends('templates.xgrow.main')

@push('after-styles')
    <style>
        .modal__content {
            padding: 40px 52px;
        }

        @media screen and (max-width: 768px) {
            .modal__content {
                padding: 20px 26px;
            }
        }

        .modal__title {
            color: #FFF;
            font-size: 18px;
            padding-bottom: 4px;
        }

        .modal__subtitle {
            color: #FFF;
            font-size: 16px;
            font-weight: 400;
            margin-bottom: 18px;
        }

        .modal__line {
            background-color: #C4C4C4;
        }

        .bank-data {
            display: flex;
            flex-direction: column;
            margin-bottom: 36px;
        }

        .bank-data__item {
            font-weight: 600;
            font-size: 14px;
            color: #FFF;
            padding: 10px 12px;
            background: #333844;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .bank-data__item span {
            font-weight: 400;
        }

        .bank-data__item--dark {
            background: #252932;
        }

        .bank-data__title {
            margin-bottom: 0px;
            font-weight: 700;
        }

        .withdraw__available {
            margin-bottom: 20px;
            color: #FFF;
        }

        .withdraw__info {
            font-weight: 400;
            color: #FFF;
            margin-bottom: 24px;
        }

        .withdraw__available span {
            color: var(--font-color);
            font-weight: 700;
        }

        .modal__actions {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        @media screen and (max-width: 768px) {
            .modal__actions {
                flex-direction: column-reverse;
            }
        }

        .modal__actions--cancel {
            border-color: #FFF;
            color: #FFF;
            min-width: 175px;
        }

        .modal__actions--confirm {
            background: #93BC1E;
            color: #FFF;
            min-width: 175px;
        }

        .modal__actions--confirm:hover, .modal__actions--cancel:hover {
            color: #FFF;
        }

        .fa-check {
            font-size: 12px;
            margin-right: 5px;
        }
    </style>
@endpush

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/echarts@4.8.0/dist/echarts.min.js"></script>
    <script src="{{ asset('xgrow-vendor/assets/js/toast-config.js') }}"></script>
    <script src="{{ asset('js/bundle/reports-financial.js') }}"></script>
@endpush

@section('content')
    <div id="financialPage">
        <router-view></router-view>
    </div>
    @include('elements.toast')
@endsection
