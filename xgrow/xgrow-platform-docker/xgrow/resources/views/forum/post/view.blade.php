@extends('templates.monster.main')

@section('jquery')
@endsection

@push('after-styles')
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@700&display=swap" rel="stylesheet">
@endpush

@push('before-scripts')
    <script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')
@endpush

@section('content')
    <div class="row page-titles">
        <div class="col-md-6 col-8 align-self-center">
            <h3 class="mb-0 mt-0"><i class="mdi mdi-playlist-plus"></i> Post</h3>
            <ol class="breadcrumb fandone-bc ">
                <li class="fandone-bc-item"><a href="/">Home</a></li>
                <li>
                    <div class="arrow"></div>
                </li>
                <li class="fandone-bc-item"><a href="/forum">Fórum</a></li>
                <li>
                    <div class="arrow"></div>
                </li>
                <li>{{$post->title}}</li>
            </ol>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-message-text text-muted" style="font-size: 1.75rem; color: #CCCCCC"></i>
                <h3 class="text-gray-dark ml-2"> {{$post->title}}</h3>
            </div>
            <div class="ml-md-4">
                <?php $tags = explode(';', $post->tags); ?>
                @foreach($tags as $tag)
                    <span class="badge badge-pill badge-info"> {{$tag}}</span>
                @endforeach
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-md-2 my-3">
                    <div class="profile-img d-flex justify-content-sm-left justify-content-md-center">
                        <img src="http://localhost:8000/images/profile.png" alt="user"
                             style="width: 80px; height: 80px; border-radius: 50%">
                    </div>
                </div>
                <div class="col-sm-12 col-md-10">
                    <div class="post-header mb-4">
                        @if(!empty($post->subscribers->name))
                            <h5>{{$post->subscribers->name}} - {{ date('j F', strtotime($post->created_at)) }}</h5>
                        @else
                            <h5>{{$post->platforms_users->name}} - {{ date('j F', strtotime($post->created_at)) }}</h5>
                        @endif
                    </div>
                    <div class="post-body">
                        {!! $post->body !!}
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <i class="far fa-heart text-danger"></i>
                                <span class="text-info">{{$post->likes}} curtidas</span>
                            </div>
                            <div>
                                <p class="text-info">
                                    <i class="mdi mdi-reply-all"></i>
                                    <span id="respostas">{{$replies->count()}}</span>
                                    {{$replies->count() == 1 ? 'Resposta': 'Respostas'}}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="post-footer">
                        <a href="{{route('forum.index')}}" class="btn btn-info">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <a href="{{route('post.allow', $post->id)}}"
                           class="btn btn-success {!! $post->approved ? 'disabled' : '' !!}">
                            <i class="fas fa-check"></i> Aprovar
                        </a>
                        <a href="{{route('post.deny', $post->id)}}"
                           class="btn btn-danger {!! $post->approved ? '' : 'disabled' !!}">
                            <i class="fas fa-times"></i> Reprovar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
