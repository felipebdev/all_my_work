@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">
                @if($apperance["sessionTitle"])
                    {{ $sessionTitle }}
                @endif
            </h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Automatizar breadcrumb</a></li>
                <li class="breadcrumb-item active">Nível atual</li>
            </ol>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="card-body">
                    <h5 class="card-title">{{ $highlight['title'] }}</h5>
                    <p class="card-text">{{ $highlight['text'] }}</p>
                    <p class="card-text"><small class="text-muted">{{ $highlight['date'] }}</small></p>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="card bg-dark text-white">
                    <img class="card-img" src="{{ $highlight['image'] }}" alt="Imagem do card">
                    <div class="card-img-overlay">
                        <p class="card-text">{{ $highlight['title'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        Segundo título?
                    </div>
                </div>
            </div>
        </div>
        @foreach($cards as $card)

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-3" style="max-width: 540px;">
                        <div class="row no-gutters">
                            <div class="col-md-4">
                                @if($apperance["contentImage"])
                                    <img src="{{ $card['image'] }}" class="card-img">
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    @if($apperance["contentTitle"])
                                        <h5 class="card-title">{{ $card["title"] }}</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    @if($apperance["contentText"])
                        <p class="card-text">{{ substr($card["text"], 0, 120) }}</p>
                        <a href="#" class="btn btn-secondary">Leia mais</a>
                    @endif
                </div>
            </div>
            <div class="row">&nbsp;</div>
        @endforeach
    </div>
@endsection
