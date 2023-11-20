 function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $(`#${up_image_source}`).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]); // convert to base64 string
            }
        }

        function showLoading(div){
            $(div).addClass('d-flex').addClass('justify-content-center')
            $(div).empty().append(`<center>
                        <br />
                        <div class="spinner-border text-primary text-center" role="status">
                          <span class="sr-only">Loading...</span>
                        </div>
                    </center>`);
        }


        function hideLoading(div){
            $(div).removeClass('d-flex').removeClass('justify-content-center')
            $(div).empty();
        }

        $(document).ready(
            function () {

                $(".up_image_button").click(function() {
                    up_image_source = $(this).data('source'); //variável global
                    const div_upload_file = `upimage_${up_image_source}`
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

                $("#form_unsplash").submit(
                    function (event){

                      event.preventDefault();

                      var search = $('#search_unsplash').val();

                      var url = "https://api.unsplash.com/search/photos?query=" + search + "&client_id=vTvXvMxE-RK8XtKHphbOSiLCBPVcQf3HenIQMzyTyXg";

                      showLoading('#unsplash_panel');

                      $.ajax({
                        method: 'GET',
                        url: url,
                        success: function (data) {
                          hideLoading('#unsplash_panel');
                          data.results.forEach(photo => {

                            $('#unsplash_panel').append(`
                                <div class="col col-md-3 p-3">
                                    <img src="${photo.urls.small}" class="unsplash_images" style="cursor: pointer">
                                </div>
                            `);

                          });

                          updateImgClick();

                        }
                      });

                    }
                )

                function updateImgClick(){
                    $(".gallery_images, .unsplash_images").click(function() {
                        const src = $(this).prop('src');
                        $(`#${up_image_source}`).attr('src', src);
                        $(`#${up_image_source}_upimage_type`).val(src);
                        $('#upImageModal').modal('hide');
                    });
                }
                
                function updateUploadFile(){
                    $(".upload_upimage").change(function() {
                        readURL(this);
                        $(`#${up_image_source}_upimage_type`).val('local');
                        $('#upImageModal').modal('hide');
                    });
                }

                $("#gallery_id").change(function() {
                    showLoading('#gallery_panel');
                    const gallery_id = $(this).val();
                    $.ajax({
                        type: 'POST',
                        url:' {{ Route('gallery.images') }}',
                        dataType: 'json',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        data: {
                            '_token': "{{ csrf_token() }}",
                            gallery_id
                        },
                        success: function (data) {
                            const {images, path} = data;
                            hideLoading('#gallery_panel');
                            if(images.length > 0){
                                images.forEach(function(image){
                                    const {id, filename} = image;
                                    $('#gallery_panel').append(`
                                        <div class="col col-md-3 p-3">
                                            <img src="${path}/${filename}" class="gallery_images" style="cursor: pointer">
                                        </div>
                                    `);
                                });

                                updateImgClick();
                            }
                            else{
                                $('#gallery_panel').append(`Nenhuma imagem nessa galeria`);
                            }
                            
                        }
                    });
                });
            }
        )
