@extends('templates.monster.main')

@section('jquery') {{-- Including this section to override it empty. Using jQuery from webpack build --}} @endsection

@push('before-scripts')
    <script src="{{ mix('/js/home-two.js') }}"></script>
@endpush

@section('content')

    <div class="card">
        <div class="card-body">

            <h4 class="font-weight-bold py-3 mb-4">Home Two</h4>

            <p>This page is the second example of the basic layout to get you started.</p>

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

            <alert-component>
                Component reused on multiple pages!
            </alert-component>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            @foreach(range(1,15) as $i)

                <h5>Chapter {{ $i }}</h5>

                <p class="mb-5">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer maximus massa enim, sit amet dictum arcu
                    pharetra eget. Aliquam aliquet ac libero quis fringilla. Morbi consectetur tortor et tempus sollicitudin.
                    Pellentesque porttitor venenatis nisl ultrices dignissim. Pellentesque molestie cursus augue, quis efficitur
                    orci posuere vel. Quisque mi tortor, pulvinar in nibh quis, vestibulum gravida sem. Pellentesque id purus a
                    felis bibendum scelerisque. Cras mauris diam, scelerisque at velit ac, malesuada sagittis magna. Cras
                    vestibulum vehicula nunc, eu fermentum felis maximus non. Sed mollis, mi quis sagittis ornare, ex dui
                    tincidunt erat, sed luctus dolor dui id augue. Maecenas sodales lacinia nisi at dictum. Quisque in mi
                    pulvinar, luctus dolor in, bibendum lorem. Quisque accumsan metus urna, vel maximus eros pulvinar vel.
                    Phasellus id sapien vitae urna euismod convallis.
                </p>

            @endforeach

        </div>
    </div>

@endsection
