<div class="row">
	<div class="col-lg-6 col-md-12">
		<div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
			<input id="platform_name" name="platform_name" autocomplete="off" type="text" spellcheck="false"
				value="{{$config[0]['name'] ?? ''}}" required @if(isset($config[0]['name'])) disabled @endif />
			<label for="platform_name">Nome</label>
		</div>
		<div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
			<input id="platform_url" name="platform_url" autocomplete="off" type="text" spellcheck="false"
				value="{{ $config[0]['url'] ?? '' }}" required disabled />
			<label for="platform_url">Plataforma Xgrow</label>
		</div>
		<div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
			<input type="email" id="reply_to_email" name="reply_to_email" autocomplete="off" type="text" spellcheck="false"
				value="{{ $config[0]['reply_to_email'] ?? '' }}" />
			<label>Responder para (Email)</label>
		</div>
	</div>
	<div class="col-lg-6 col-md-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
			<input id="reply_to_name" name="reply_to_name" autocomplete="off" type="text" spellcheck="false"
				value="{{ $config[0]['reply_to_name'] ?? '' }}" />
			<label>Responder para (Nome no e-mail)</label>
		</div>
		<div class="d-flex flex-column">
			<div class="xgrow-floating-input mui-textfield mui-textfield--float-label w-100 me-lg-4">
				<input id="url_official" name="url_official" autocomplete="off" type="text" spellcheck="false"
					value="{{ $config[0]['url_official'] ?? '' }}" required />
				<label for="url_official">Endereço Oficial</label>
			</div>
			<div class="form-check form-switch">
				<input type="checkbox" name="active" id="exampleCheck1" class="form-check-input" {{$config[0]['active'] == 1 ? 'checked' : ''}} />
				<label for="exampleCheck1" class='form-check-label'>Online</label>
			</div>
		</div>
	</div>
</div>
