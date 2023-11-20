<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="email_area">Área</label>
            <select class="form-control" name="email_area" required>
                @foreach($data['areas'] as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="subject">Assunto</label>
            <input type="text" class="form-control" id="subject" name="subject" value="{{$data['email']['subject'] ?? ''}}" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="from">From</label>
            <input type="text" class="form-control" id="from" name="from" value="{{$data['email']['from'] ?? ''}}" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="message">Mensagem</label>
            <textarea name="message" id="message" class="content_html form-control" cols="10" rows="3">{{ $data['email']['message'] ?? '' }}</textarea>
        </div>
    </div>
</div>

