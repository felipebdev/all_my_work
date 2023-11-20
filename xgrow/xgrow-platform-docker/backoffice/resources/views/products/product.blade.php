@if($product->platform)
<div class="product-container p-3 mb-3 row">
    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-12">
        <div class="product-image">
            <img
                src="{{
                    $product->image != null
                    ? $product->image->filename
                    : asset('images/course_default.svg')
                }}"
            />
        </div>
    </div>
    <div class="product-content col-xl-9 col-lg-8 col-md-8 col-sm-8 col-12 d-flex flex-column justify-content-between">
        <div class="product-info d-flex justify-content-between align-items-end flex-wrap-reverse">
            <div class="mt-3">
                <h2>{{ $product->name }}</h2>
                <small class="mb-2">categoria</small>
                <p class="m-0">{{ $product->platform->name }} | {{ ($product->platform->client->first_name ?? '') .' '.($product->platform->client->last_name ?? '') }}</p>

                @if ($product->analysis_status == 'approved')
                    <span class="approved">
                        <i class="fas fa-check"></i>
                        Aprovado
                    </span>
                @endif

                @if ($product->analysis_status == 'under_analysis')
                    <span class="under-analysis">
                        <i class="far fa-clock"></i>
                        Em análise
                    </span>
                @endif

                @if ($product->analysis_status == 'refused')
                    <span class="refused">
                        <i class="far fa-thumbs-down"></i>
                        Recusado
                    </span>
                @endif

                @if ($product->analysis_status == 'blocked')
                    <span class="blocked">
                        <i class="fas fa-ban"></i>
                        Bloqueado
                    </span>
                @endif
            </div>
            <div class="product-actions mt-3">
                <a href="{{ route("products.show", $product->id) }}" class="details">
                    <i class="fas fa-eye"></i>
                    Ver detalhes
                </a>
            </div>
        </div>
        <div class="product-bottom">
            <div class="d-flex flex-wrap justify-content-end align-items-center">
                {{-- <div class="product-number-sales mt-3">
                    <i class="fas fa-shopping-cart"></i>
                    100
                </div> --}}

                <div class="product-actions d-flex flex-wrap">
                    @if ($product->analysis_status != 'approved')
                        <a
                            href="javascript:void(0)"
                            class="approved mt-3 btn-change-status"
                            data-status="approved"
                            data-idproduct="{{ $product->id }}"
                        >
                            <i class="fas fa-check"></i>
                            Aprovar
                        </a>
                    @endif

                    @if ($product->analysis_status != 'under_analysis')
                        <a
                            href="javascript:void(0)"
                            class="under-analysis mt-3 btn-change-status"
                            data-status="under_analysis"
                            data-idproduct="{{ $product->id }}"
                        >
                            <i class="far fa-clock"></i>
                            Analisar
                        </a>
                    @endif

                    @if ($product->analysis_status != 'refused')
                        <a
                            href="javascript:void(0)"
                            class="refused mt-3 btn-change-status"
                            data-status="refused"
                            data-idproduct="{{ $product->id }}"
                        >
                            <i class="far fa-thumbs-down"></i>
                            Recusar
                        </a>
                    @endif

                    @if ($product->analysis_status != 'blocked')
                        <a
                            href="javascript:void(0)"
                            class="blocked mt-3 btn-change-status"
                            data-status="blocked"
                            data-idproduct="{{ $product->id }}"
                        >
                            <i class="fas fa-ban"></i>
                            Bloquear
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
