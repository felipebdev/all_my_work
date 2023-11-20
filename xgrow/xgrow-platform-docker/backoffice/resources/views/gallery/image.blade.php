@extends('templates.horizontal.main')

@section('jquery')
<script>
    $(document).ready(

        function (){

            $('#imagesModal').on('hidden.bs.modal', function () {
                location.reload();
            });

            Dropzone.options.addImages = {
                acceptedFiles: 'image/*',
                dictDefaultMessage: 'Arraste e solte as imagens aqui ou clique para enviá-las',
                dictInvalidFileType: 'Você não pode carregar arquivos deste tipo.',
            };

        }

    )
    

</script>
@endsection

@section('styles')
<style>
    /* picture img {
        max-height: 200px;
    } */

    .dropzone {
        border: 2px dashed #0087F7;
        border-radius: 5px;
        background: white;
    }

    .dropzone .dz-message {
        font-weight: 400;
    }

    .dropzone .dz-message .note {
        font-size: 0.8em;
        font-weight: 200;
        display: block;
        margin-top: 1.4rem;
    }

    .gallery-images {
        position: relative;
    }

    .gallery-images img {
        object-fit: cover;
        height: 200px;
        width: 100%;
        max-width: 100%;
    }

    .gallery-images .btn {
        position: absolute;
        top: 0;
        right: 0;
        margin: 33px auto;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        cursor: pointer;
    }
</style>
@endsection

@push('before-scripts')
<script src="{{ mix('/js/home-one.js') }}"></script>
@endpush


@section('content')

<div class="row page-titles">
    <div class="col-md-6 col-8 align-self-center">
        <h3 class="text-themecolor mb-0 mt-0">Galeria</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('gallery.index') }}">Álbum</a></li>
            <li class="breadcrumb-item active">{{$gallery->name}}</li>
        </ol>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-end pb-4">
            <a href="#"  class="btn btn-rounded btn-outline-primary" data-toggle="modal" data-target="#imagesModal">
                <i class="fa fa-upload"></i> Enviar
            </a>
        </div>
        <div class="form-row">
            @foreach ($gallery->images as $image)
            <div class="col-lg-3 col-md-4 col-6 gallery-images">
                <a href="{{Storage::disk('gallery')->url($image->filename)}}" class="d-block mb-4 h-100" data-lightbox="gallery-images">
                    <img class="img-fluid img-thumbnail" src="{{ asset('gallery/' . $image->filename)}}">
                </a>
                <a href="{{ route('gallery.image.destroy', [$gallery->id, $image->id]) }}" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal -->
<div id="imagesModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="imagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-0">
                <h5 class="modal-title  mb-auto mt-auto" id="imagesModalLabel">
                    Enviar imagens
                </h5>
                <button type="button" class="close mb-auto mt-auto" data-dismiss="modal" aria-label="Close">
                    <span class="text-danger" aria-hidden="true">
                        <i class="fas fa-window-close"></i>
                    </span>
                </button>
            </div>
            <div class="modal-body">
                <div id="dropzone">
                    <form action="{{ route('gallery.image.store', [$gallery->id]) }}" method="PUT" class="dropzone needsclick dz-clickable form-row justify-content-center" id="addImages">
                        {{ csrf_field() }}
                        
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection