@extends('templates.horizontal.main')

@section('jquery')

@endsection


@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush

@push('after-scripts')

    

@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Templates</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active">Templates Plataforma</li>
        </ol>
    </div>
</div>
<div class="card">
    <div class="card-body">

        <div class="d-flex justify-content-end">
             {!! icon_link_to_route('star','templatePlatform.create', 'Novo template', null, ['class' => 'btn btn-rounded btn-outline-primary']) !!}
        </div>

        <h2>Templates</h2>
        <div class="row el-element-overlay">
                @forelse ($templates as $template)
                    <div class="col-lg-3 col-md-6">
                            <div class="card">
                                <div class="el-card-item">
                                    <div class="el-card-avatar el-overlay-1">
                                    
                                            @if(isset($template->thumb->filename))
                                              <img src="{{ asset('uploads'. '/' . $template->thumb->filename) }}" alt="user" /> 
                                            @else
                                              <img src="{{ asset('images/avatar.png') }}" alt="user" />  
                                            @endif
                                    
                                            <div class="el-overlay ">
                                                <ul class="el-info">

                                                    <li>
                                                        {!! icon_link_to_route('trash','templatePlatform.destroy', '', $template->id, ['class' => 'btn default btn-outline']) !!}
                                                    </li>
                                                    
                                                    <li>
                                                        {!! icon_link_to_route('edit','templatePlatform.edit', '', $template->id, ['class' => 'btn default btn-outline']) !!}
                                                    </li>
                                                </ul>
                                            </div>
                                    </div>
                                    <div class="el-card-content">
                                        <h3 class="box-title">{{ $template->name }}{{($template->active==0)?'-Inativo':''}}</h3>
                                        <small>
                                            {{ $template->description }}
                                        </small>
                                     </div>
                                </div>
                            </div>
                        </div>
                @empty
                    Não há templates  cadastrados
                @endforelse
        </div>

    </div>
</div>


@endsection