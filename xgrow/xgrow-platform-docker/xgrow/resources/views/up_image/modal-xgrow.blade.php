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

        .unsplash_images, .gallery_images {
            cursor: pointer;
            width: 147px;
            height: 82px;
            object-fit: cover;
        }

        .gallery_images {
            object-fit: scale-down;
        }

    </style>
@endpush

@push('after-scripts')
    <script type="text/javascript">
        function readURL(input) {
            if (input.files && input.files[0]) {
                const imageMaxSize = 15; //mb
                if (input.files[0].size > (imageMaxSize + 1000000)) {
                    $(input).val('')
                    $('#alert-up-image').html(`Tamanho da imagem não pode ser superior a ${imageMaxSize} mb`)
                    $('#alert-up-image').removeClass('d-none');
                    return false
                } else {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(`#${up_image_source}`).attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]); // convert to base64 string
                    return true
                }
            }
        }

        function showLoading(div) {
            $(div).addClass('d-flex').addClass('justify-content-center');
            $(div).empty().append(`<div style="text-align: center;">
                                        <br />
                                        <div class="spinner-border text-primary text-center" role="status">
                                          <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>`);
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
                          <input class="form-control upload_upimage"
                            id="${up_image_source}_upimage"
                            name="${up_image_source}_upimage"
                            type="file"
                            accept="{{$restrictAcceptedFormats ?? 'image/*'}}">
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

                    var url = 'https://api.unsplash.com/search/photos?query=' + search +
                        '&client_id=vTvXvMxE-RK8XtKHphbOSiLCBPVcQf3HenIQMzyTyXg&per_page=12&page=1';

                    showLoading('#unsplash_panel');

                    $.ajax({
                        method: 'GET',
                        url: url,
                        success: function (data) {
                            hideLoading('#unsplash_panel');
                            //147*82
                            data.results.forEach(photo => {
                                $('#unsplash_panel').append(`
                                                    <img src="${photo.urls.small}" alt="${photo.user.name}" class="unsplash_images col-sm-6 col-md-4 my-2">
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
                    if(readURL(this)){
                        $(`#${up_image_source}_upimage_type`).val('local');
                        $(`#${up_image_source}_upimage_file_id`).val(0);
                        $('#upImageModal').modal('hide');
                    }
                });
            }

            $('#gallery_id').change(function () {
                showLoading('#gallery_panel');
                const gallery_id = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: ' {{ Route('gallery.images') }}',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        '_token': "{{ csrf_token() }}",
                        gallery_id,
                    },
                    success: function (data) {
                        const {
                            images,
                            path
                        } = data;
                        hideLoading('#gallery_panel');
                        if (images.length > 0) {
                            images.forEach(function (image) {
                                const {
                                    id,
                                    filename
                                } = image;
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

        function xgrowUpImageModalStart() {
            $('#search_unsplash').keydown(function (e) {
                if (e.keyCode === 13) {
                    e.preventDefault();
                    $('#unsplash_btn_search').trigger('click');
                    return false;
                }
            });

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
                                              <input class="form-control upload_upimage" id="${up_image_source}_upimage" name="${up_image_source}_upimage" type="file" accept="image/*">
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

                    var url = 'https://api.unsplash.com/search/photos?query=' + search +
                        '&client_id=vTvXvMxE-RK8XtKHphbOSiLCBPVcQf3HenIQMzyTyXg&per_page=12&page=1';

                    showLoading('#unsplash_panel');

                    $.ajax({
                        method: 'GET',
                        url: url,
                        success: function (data) {
                            hideLoading('#unsplash_panel');
                            //147*82
                            data.results.forEach(photo => {
                                $('#unsplash_panel').append(
                                    `<img src="${photo.urls.small}" alt="${photo.user.name}"
                                        class="unsplash_images col-sm-6 col-md-4 my-2">
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
                    if(readURL(this)){
                        $(`#${up_image_source}_upimage_type`).val('local');
                        $(`#${up_image_source}_upimage_file_id`).val(0);
                        $('#upImageModal').modal('hide');
                    }
                });
            }

            $('#gallery_id').change(function () {
                showLoading('#gallery_panel');
                const gallery_id = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: ' {{ Route('gallery.images') }}',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        '_token': "{{ csrf_token() }}",
                        gallery_id,
                    },
                    success: function (data) {
                        const {
                            images,
                            path
                        } = data;
                        hideLoading('#gallery_panel');
                        if (images.length > 0) {
                            images.forEach(function (image) {
                                const {id, filename} = image;
                                $('#gallery_panel').append(`
                                        <img src="${path}/${filename}" class="gallery_images col-sm-6 col-md-4 my-2"
                                            alt="icone">
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
                // upImageModalStart(); Não usar
                xgrowUpImageModalStart();
            },
        );

    </script>
@endpush

<div class="modal-sections modal fade" id="upImageModal" tabindex="-1" aria-labelledby="upImageModalModal"
     aria-hidden="true" style="z-index: 2050 !important;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div class="modal-header">
                <p class="modal-title" id="deleteSectionModal">Selecionar imagem</p>
            </div>

            <div class="modal-body d-block" style="text-align: left">

                <div class="row">
                    <div class="col-sm-12">
                        <div class="xgrow-card card-dark shadow-none mt-4">
                        <div class="xgrow-card-body">
                                <div id="input_files">
                                    <div id="upimage_image">
                                        <label for="file">Fazer upload:</label>
                                        <input class="form-control upload_upimage" id="image_upimage" name="image_upimage" type="file" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-danger d-none" id="alert-up-image">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal" aria-label="Close">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</div>
