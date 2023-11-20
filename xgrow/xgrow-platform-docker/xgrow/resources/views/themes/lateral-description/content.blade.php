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
        <div class="row justify-content-sm-center">
            @foreach($cards as $card)
                <div class="col-md-6">
                    @if($apperance["contentImage"])
                    <img class="w-auto p-3" src="{{$card["image"]}}" alt="Card image cap">
                    @endif
                </div>
                <div class="col-md-6">
                    @if($apperance["contentTitle"])
                    <div class="card-header">
                        <p class="card-text">{{$contentTitle}}</p>
                    </div>
                    @endif
                    <div class="card-body">
                        @if($apperance["contentText"])
                        <p class="card-text">{{$card["text"]}}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
