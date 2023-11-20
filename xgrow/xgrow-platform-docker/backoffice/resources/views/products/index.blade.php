@extends('templates.horizontal.main')

@section('jquery')
@endsection

@push('after-styles')
    <link rel="stylesheet" href="{{ asset("css/pages/products.css") }}">
@endpush

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')
    <script>
        $(document).ready(function () {
            $('.btn-change-status').click(function () {
                const status = $(this).data('status');
                const id = $(this).data('idproduct');
                const url = @json(route('products.change.status', ':id')).replace(/:id/g, id);

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        'analysis_status': status,
                        '_token': "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        alert(response.message);
                        window.location.reload();
                    },
                    error: function (error) {
                        alert(error.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Produtos</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Produtos</li>
            </ol>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('products.header')

            <div class="tab-content" id="products-content">
                <div
                    class="tab-pane fade show active container-fluid"
                    id="tab-all"
                    role="tabpanel"
                    aria-labelledby="tab-all"
                >
                    @forelse ($all_products as $product)
                        @include('products.product', ['product' => $product])
                    @empty
                        <p class="text-center">Nenhum produto cadastrado</p>
                    @endforelse
                </div>
                <div
                    class="tab-pane fade container-fluid"
                    id="tab-approved"
                    role="tabpanel"
                    aria-labelledby="tab-approved"
                >
                    @forelse ($approved_products as $product)
                        @include('products.product', ['product' => $product])
                    @empty
                        <p class="text-center">Nenhum produto aprovado</p>
                    @endforelse
                </div>
                <div
                    class="tab-pane fade container-fluid"
                    id="tab-under-analysis"
                    role="tabpanel"
                    aria-labelledby="tab-under-analysis"
                >
                    @forelse ($analysis_products as $product)
                        @include('products.product', ['product' => $product])
                    @empty
                        <p class="text-center">Nenhum produto em análise</p>
                    @endforelse
                </div>
                <div
                    class="tab-pane fade container-fluid"
                    id="tab-refused"
                    role="tabpanel"
                    aria-labelledby="tab-refused"
                >
                    @forelse ($refused_products as $product)
                        @include('products.product', ['product' => $product])
                    @empty
                        <p class="text-center">Nenhum produto recusado</p>
                    @endforelse
                </div>
                <div
                    class="tab-pane fade container-fluid"
                    id="tab-blocked"
                    role="tabpanel"
                    aria-labelledby="tab-blocked"
                >
                    @forelse ($blocked_products as $product)
                        @include('products.product', ['product' => $product])
                    @empty
                        <p class="text-center">Nenhum produto bloqueado</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection