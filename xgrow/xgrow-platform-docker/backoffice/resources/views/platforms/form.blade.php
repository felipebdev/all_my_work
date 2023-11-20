@section('jquery')
<!--
    <script src="{{asset('vendor/slugify/speakingurl.min.js')}}"></script>
    <script src="{{asset('vendor/slugify/slugify.min.js')}}"></script>
    <script src="{{asset('vendor/slugify/jquery-slugify.js')}}"></script>
    -->

@endsection
@push('before-styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}">
@endpush
@push('after-scripts')

    <script>
		$('#restrict_ips').click(function () {
			if ($('#restrict_ips').prop('checked')) {
				$('#div_ips_available').show();
			} else {
				$('#div_ips_available').hide();
			}
			// if($(this).prop('checked'))
		});

		$('.add_image').change(function (evt) {
			$('#image').attr('src', URL.createObjectURL(evt.target.files[0]));
			$('#remove_image').val(false);
		});

		$('.remove_image').click(function () {
			$('#image').attr('src', "{{ asset('images/course_default.svg') }}");
			$('#remove_image').val(true);
		});

		//$('#slug').urlSlugify('#platform_url', 'https://la.xgrow.com/', '-', 20);

    </script>

    <script>
		window.APP_URL_LEARNING_AREA = `<?= env('APP_URL_LEARNING_AREA') ?>`;
    </script>
    <script type="module" src="{{ asset('/js/platforms.js') }}"></script>
@endpush

@if (count($errors) > 0)
    <div class="row">
        <div class="col col-sm-6 offset-sm-3">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        {{ $error }} <br/>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="customer_id">Cliente</label>
            <select class="form-control" name="customer_id" required>
                <option></option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}"
                        {{ isset($platform) && $platform->customer_id == $customer->id ? 'selected' : '' }}>
                        {{ $customer->first_name . ' ' . $customer->last_name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="platform_name">Nome</label>
            <input type="text" class="form-control" id="platform_name" name="platform_name"
                   value="{{$platform->name ?? ''}}" required>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <!--
            <label for="platform_name">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" value="{{$platform->slug ?? ''}}"
                   maxlength="20" required>


                   -->

                <x-ui.input id="slug" name="slug" type="slug" maxlength="20" label="Endereço amigável (slug)" value="{{ str_replace(['http://', 'https://'], ['', ''], ($platform->slug ?? '')) }}" />


        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <!--
            <label for="platform_url">Url</label>
            <input type="url" class="form-control" id="platform_url" name="platform_url"
                   value="{{$platform->url ?? 'https://'}}" required>
                   -->

                <x-ui.input id="platform_url" name="platform_url" type="slug" maxlength="200" prepend="https://" label="URL" value="{{ str_replace(['http://', 'https://'], ['', ''], ($platform->url ?? '')) }}" />

        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <input name="restrict_ips" id="restrict_ips"
                   type="checkbox" {{ $platform->restrict_ips == 1 ? "checked='checked'": ""}}>
            <label for="restrict_ips" style="color: #c00">Restringir acesso por ip</label>
            <div id="div_ips_available" {!! $platform->restrict_ips == 0 ? "style='display: none'": "" !!}>
                <input type="text" data-role="tagsinput" id="ips_available" name="ips_available"
                       value="{{$platform->ips_available ?? ''}}">
            </div>
        </div>
    </div>
</div>
@if(Route::current()->getName() == 'platforms.edit')

    <div class="row">

        <div class="form-group">
            <label for="recipient_id">Código do recebedor (gerado automaticamente):</label>
            <input type="text" class="form-control" id="recipient_id" name="recipient_id"
                   value="{{$platform->recipient_id ?? ''}}">
        </div>

    </div>
@endif
<div class="row el-element-overlay">
    @forelse ($templates as $template)
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-title">
                    <div class="custom-control custom-radio">
                        <input type="radio" id="template{{ $template->id }}" name="templates"
                               value="{{ $template->id }}"
                               {{ isset($platform) && $platform->template_id == $template->id ? 'checked' : '' }}  class="custom-control-input">
                        <label class="custom-control-label"
                               for="template{{ $template->id }}">{{ $template->name }}</label>
                    </div>
                </div>

                @if(isset($template->thumb->filename))
                    <img class="card-img-top"
                         src="{{ asset(config('constants.imgTemplatesDir') . $template->thumb->filename) }}"
                         alt="Card image cap">
                @endif
            </div>
        </div>
    @empty
        Não há templates cadastrados
    @endforelse
</div>

@if(Route::current()->getName() == 'platforms.edit')
    <div class="row">
        <div class="col-12 d-flex flex-wrap align-items-center">
            <img id="image" style="border-radius: 5px; width: 200px; height: auto;"
                 src="{{$platform->featured_image ?? asset('images/course_default.svg')}}"/>
            <div class="">
                <input class="ml-2 mt-2 add_image" type="file" name="featured_image" id="featured_image"/>
                <input id="remove_image" type="hidden" name="remove_image"/><br/>

                @if($platform->featured_image !== null)
                    <a class="ml-2 mt-5 remove_image" style="color: red" href="javascript:void(0)">Remover imagem
                        atual</a>
                @endif
            </div>
        </div>
    </div>
@endif
