{{-- @push('before-styles')
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.4/dist/tailwind.min.css" rel="stylesheet">
@endpush --}}


<div class="dom-container">
    <div class="dom-title">Estamos carregando o conteúdo da página... Por favor, aguarde.</div>

    <div class="container mx-auto">
        <div class="max-w-sm mx-auto md:max-w-lg">
            <div class="w-full">
                <div class="bg-white rounded">
                    <div class="bg-gray-200 h-48 p-3 overflow-hidden animate-pulse"> </div>
                    <div class="h- p-3">
                        <div class="grid grid-cols-3 gap-4 mt-2">
                            <div class="h-8 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-8 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-8 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-8 col-span-2 bg-gray-200 rounded animate-pulse"></div>
                            <div class=" h-8 bg-gray-200 rounded animate-pulse"></div>
                            <div class="..."></div>
                            <div class="col-span-2 ..."></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dom-xgrow-logo">
        <img id="dom-logo" src="{{ asset('xgrow-vendor/assets/img/logo/dark.svg') }}" alt="XGROW" width="100">
    </div>
</div>
