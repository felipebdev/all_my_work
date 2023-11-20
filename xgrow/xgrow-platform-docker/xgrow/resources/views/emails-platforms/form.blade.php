@include('elements.alert')
<div class="row mb-3">
    <div class="col-xl-6 col-md-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            @if ($data["type"] == "edit")
                <input type="hidden" name="email_id" value="{{ $email->email_id }}">
                
                <select class="xgrow-select" disabled>
                    <option value="0" disabled="" selected="" hidden=""></option>
                    @foreach($data['email_type'] as $item)
                        <option
                            value="{{ $item->id }}" {{isset($email->email_id) && $email->email_id == $item->id ? 'selected' : ''}}>{{ $item->subject }}</option>
                    @endforeach
                </select>
                <label for="email_id">*Tipo de Email</label>
            @else
                <select class="xgrow-select" name="email_id" id="email_id" required>
                    <option value="0" disabled="" selected="" hidden=""></option>
                    @foreach($data['email_type'] as $item)
                        <option
                            value="{{ $item->id }}"
                            {{
                                isset($data['email_selected']) ?
                                    ($data['email_selected'] == $item->id ? 'selected' : '') :
                                    ($item == $data['email_type']->first() ? 'selected' : '')
                            }}
                        >{{ $item->subject }}</option>
                    @endforeach
                </select>
                <label for="email_id">*Tipo de Email</label>
            @endif
            
        </div>
    </div>

    <div class="col-xl-6 col-md-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <select class="xgrow-select" name="from" id="from" required>
                <option value="0" disabled="" {{ !isset($email->from) ? 'selected' : '' }} hidden=""></option>
                @foreach($data['from'] as $item)
                    <option
                        value="{{ $item->email }}" {{isset($email->from) && $email->from == $item->email ? 'selected' : ''}}>{{ $item->name }}
                        - [{{ $item->email }}]
                    </option>
                @endforeach
                <option value="naoresponda@xgrow.com.br" {{ isset($email->from) && $email->from == "naoresponda@xgrow.com.br" ? 'selected' : ''}}>Não Responda - [naoresponda@xgrow.com.br]</option>
            </select>
            <label for="from">*Remetente</label>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
            <input type="text" name="subject" id="subject" value="{{ $email->subject ?? '' }}" autocomplete="off"
                   type="text" required>
            <label>Assunto</label>

        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 col-md-12">
        {!! Form::textarea('message', $email->message ?? '', [ 'rows' => '10', 'id' => 'message', 'style' => 'resize: none', 'spellcheck' => 'false' ]) !!}
    </div>
</div>
