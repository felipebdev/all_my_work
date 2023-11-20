@extends('templates.monster.main')

@section('content')

    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="text-themecolor mb-0 mt-0">Planos</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/plans">Planos</a></li>
                <li class="breadcrumb-item active">Getnet</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card" style="width: 18rem;">
                <div class="card-body">
                    <h5 class="card-title">Planos sincronizados</h5>

                    @forelse($links as $item)
                    <div class="card-body">
{{--                            <a href="{{ url('/').$item['link'] }}" class="card-link">{{ $item['name'] }}</a>--}}
                            <a href="{{ url('/').$item['link'] }}" class="btn btn-primary">{{ $item['name'] }}</a>
                    </div>
                    @empty
                        <span>Nenhum plano encontrado</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
