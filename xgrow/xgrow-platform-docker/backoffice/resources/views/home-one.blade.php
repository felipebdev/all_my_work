@extends('templates.monster.main')

@section('jquery') {{-- Including this section to override it empty. Using jQuery from webpack build --}} @endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@section('content')

    <div class="card">
        <div class="card-body">

            <h4 class="font-weight-bold py-3 mb-4">Home One</h4>

            <p>This page is an example of basic layout to get you started.</p>

            <div class="w-100 p-3"></div>

            <p>
                Visit complete demo,
                <strong>
                    <a href="https://monster-laravel-demo.wrapbyte.com/monster/" target="_blank">
                        click here
                    </a>
                </strong>.
            </p>

            <div class="w-100 p-3"></div>

            <p> The Carousel and Notifications below are Vue components.</p>

            <div class="w-100 p-3"></div>

            <div class="col-6">
                @inject('itemsCarousel', 'carousel')
                <carousel-component :items="{{ json_encode($itemsCarousel->get()) }}"></carousel-component>
            </div>

            <div class="w-100 p-3"></div>

            <alert-component></alert-component>

            <alert-component>
                Instead of default text, we can insert some we want
            </alert-component>

            <alert-component variant="warning">
                Also a prop to be used inside component
            </alert-component>

            <alert-component variant="danger">
                And another prop
            </alert-component>

        </div>
    </div>

@endsection
