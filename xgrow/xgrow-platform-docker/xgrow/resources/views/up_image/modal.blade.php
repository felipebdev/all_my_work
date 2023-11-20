@inject('galleries', 'App\Gallery')
@push('after-styles')
    <style type="text/css">
        #upImageContent {
            min-height: 300px;
        }

        #upImageContent img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
    </style>
@endpush

@push('after-scripts')
    <script type="text/javascript">
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(`#${up_image_source}`).attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        function showLoading(div) {
            $(div).addClass('d-flex').addClass('justify-content-center');
            $(div).empty().append(`<center>
                        <br />
                        <div class="spinner-border text-primary text-center" role="status">
                          <span class="sr-only">Loading...</span>
                        </div>
                    </center>`);
        }

        function hideLoading(div) {
            $(div).removeClass('d-flex').removeClass('justify-content-center');
            $(div).empty();
        }

        function upImageModalStart() {
            $('.up_image_button').click(function () {
                up_image_source = $(this).data('source'); //variável global

                $(`.nav-link`).show();
                if ($(this).data('tab_hide') != '') {
                    tab_hide = $(this).data('tab_hide').split(',');
                    tab_hide.forEach(tab => $(`#${tab}-tab`).hide());
                }

                const div_upload_file = `upimage_${up_image_source}`;

                $('#input_files div').hide();
                if ($(`#${div_upload_file}`).length == 0) {
                    $('#input_files').append(`
                            <div id="${div_upload_file}">
                              <label for="file">Fazer upload:</label>
                              <input class="form-control upload_upimage" id="${up_image_source}_upimage" name="${up_image_source}_upimage" type="file">
                            </div>
                        `);
                }

                $(`#${div_upload_file}`).show();
                updateUploadFile();
            });

            $('#unsplash_btn_search').click(
                function (event) {

                    event.preventDefault();

                    var search = $('#search_unsplash').val();

                    var url = 'https://api.unsplash.com/search/photos?query=' + search + '&client_id=vTvXvMxE-RK8XtKHphbOSiLCBPVcQf3HenIQMzyTyXg&per_page=12&page=1';

                    showLoading('#unsplash_panel');

                    $.ajax({
                        method: 'GET',
                        url: url,
                        success: function (data) {
                            hideLoading('#unsplash_panel');
                            console.log(data);
                            data.results.forEach(photo => {

                                $('#unsplash_panel').append(`
                                <div class="col col-md-3 p-3">
                                    <img src="${photo.urls.small}" alt="${photo.user.name}" class="unsplash_images" style="cursor: pointer">
                                </div>
                            `);

                            });

                            updateImgClick();

                        },
                    });

                },
            );

            function updateImgClick() {
                $('.gallery_images, .unsplash_images').click(function () {
                    const src = $(this).prop('src');
                    const copyright = $(this).prop('alt');
                    $(`#${up_image_source}`).attr('src', src);
                    $(`#${up_image_source}_upimage_type`).val('gallery');
                    $(`#${up_image_source}_upimage_file_id`).val(0);
                    $(`#${up_image_source}_upimage_url`).val(src);
                    $(`#${up_image_source}_upimage_copyright`).val(copyright);
                    $('#upImageModal').modal('hide');
                });
            }

            function updateUploadFile() {
                $('.upload_upimage').change(function () {
                    readURL(this);
                    $(`#${up_image_source}_upimage_type`).val('local');
                    $(`#${up_image_source}_upimage_file_id`).val(0);
                    $('#upImageModal').modal('hide');
                });
            }

            $('#gallery_id').change(function () {
                showLoading('#gallery_panel');
                const gallery_id = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: ' {{ Route('gallery.images') }}',
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        '_token': "{{ csrf_token() }}",
                        gallery_id,
                    },
                    success: function (data) {
                        const {images, path} = data;
                        hideLoading('#gallery_panel');
                        if (images.length > 0) {
                            images.forEach(function (image) {
                                const {id, filename} = image;
                                $('#gallery_panel').append(`
                                        <div class="col col-md-3 p-3">
                                            <img src="${path}/${filename}" class="gallery_images" style="cursor: pointer">
                                        </div>
                                    `);
                            });

                            updateImgClick();
                        } else {
                            $('#gallery_panel').append(`Nenhuma imagem nessa galeria`);
                        }

                    },
                });
            });
        }

        $(document).ready(
            function () {
                upImageModalStart();
            },
        );
    </script>
@endpush
<div id="upImageModal" class="modal-sections modal fade" style="z-index: 9999" tabindex="-1"
     aria-labelledby="deleteSectionModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header py-0">
                <h5 class="modal-title  mb-auto mt-auto" id="upImageModalLabel">
                    Selecionar imagem
                </h5>
                <button type="button" class="close mb-auto mt-auto" data-dismiss="modal" aria-label="Close">
                    <span class="text-danger" aria-hidden="true">
                        <i class="fas fa-window-close"></i>
                    </span>
                </button>
            </div>
            <div class="modal-body">

                <ul class="nav nav-tabs" id="myTabUpImage" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="upload-tab" data-toggle="tab" href="#upload" role="tab"
                           aria-controls="upload" aria-selected="true">Upload</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery" role="tab"
                           aria-controls="gallery" aria-selected="false">Galeria</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="unsplash-tab" data-toggle="tab" href="#unsplash" role="tab"
                           aria-controls="unsplash" aria-selected="false">Internet</a>
                    </li>

                </ul>
                <div class="tab-content" id="upImageContent">
                    <div class="tab-pane fade show active p-3" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                        <div class="form-group" id="input_files">
                        </div>
                    </div>

                    <div class="tab-pane fade p-3" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                        <div class="row">
                            <div class="col col-md-6">
                                <div class="form-group">
                                    <select class="form-control" id="gallery_id" name="gallery_id">
                                        <option value="" selected="selected">Selecionar álbum</option>
                                        @foreach($galleries->get() as $gallery)
                                            <option value="{{ $gallery->id }}">{{ $gallery->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="gallery_panel">
                        </div>
                    </div>

                    <div class="tab-pane fade p-3" id="unsplash" role="tabpanel" aria-labelledby="unsplash-tab">
                        <div class="row">
                            <div class="col col-md-6">
                                <form name="form_unsplash" id="form_unsplash">
                                    <div class="form-group d-flex justify-content-start">
                                        <form name="form_unsplash" id="form_unsplash">
                                            <div class="form-group d-flex justify-content-start">
                                                <input class="form-control" id="search_unsplash"
                                                       placeholder="Pesquisar imagem" name="search_unsplash"
                                                       type="search">
                                                <button type="button" class="btn btn-primary" id="unsplash_btn_search">
                                                    <i class="fa fa-search"></i></button>
                                            </div>
                                        </form>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="row" id="unsplash_panel">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
