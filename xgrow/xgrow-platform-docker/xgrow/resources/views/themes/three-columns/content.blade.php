@section('content')
    <div class="row">
        <div class="col-12">
            @inject('itemsSeedTemplate', 'seed.template')
        </div>
    </div>

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">{{ $sessionTitle }}</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Automatizar breadcrumb</a></li>
                <li class="breadcrumb-item active">Nível atual</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row el-element-overlay">
                @foreach($cards as $card)
                    <div class="col-sm-4">
                        <div class="card" style="width: 18rem;">
                            <img class="card-img-top" src="{{$card["image"]}}" alt="Card image cap">
                            <div class="card-body">
                                <p class="card-text">{{$card["text"]}}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection