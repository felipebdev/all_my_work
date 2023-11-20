@push('after-scripts')
@endpush

<div class="row comment-menu">
    <div class="col-sm-12 col-md-12 d-flex justify-content-start mb-3">
        <div class="xgrow-check me-3 d-flex align-items-center">
            {!! Form::checkbox('select_all', 1, false, ['id' => 'checkAll', 'style' => 'margin-right: 10px']) !!}
            <label onclick="$('#checkAll').click()" id="lblCheckAll" style="cursor: pointer">
                Selecionar todos
            </label>
        </div>

        <div class="mx-3">
            <span onclick="changeStatusSelected()" style="cursor: pointer">
                <i class="fa fa-retweet"></i> Mover para {{ ($approved) ? 'validação' : 'aprovados'}}
            </span>
        </div>

        <div class="mx-3">
            <span onclick="deleteSelected()" style="cursor: pointer">
                <i class="fa fa-trash"></i> Excluir
            </span>
        </div>
    </div>

    <div class="col-sm-12 col-md-12 d-flex justify-content-start flex-wrap align-items-center">

        <div style="margin-right: 25px" class="mb-3">
            <div class="xgrow-form-control">
                {!! Form::select('author_id', $authors, null, ['id' => 'author_id', 'class' => 'xgrow-select']) !!}
            </div>
        </div>

        <div style="margin-right: 25px; margin-bottom: -4px">
            <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                <input id="tags" placeholder="Assinantes" class="mui--is-empty mui--is-pristine mui--is-touched">
                <label>Assinantes</label>
                <span onclick="document.getElementById('tags').value = ''"></span>
            </div>
            {!! Form::hidden('subscriber_id', null, ['id' => 'subscriber_id']) !!}
        </div>

        <div style="margin-right: 25px" class="mb-3">
            <div class="xgrow-form-control">
                {!! Form::select('course_id', $courses, null, ['id' => 'course_id', 'class' => 'xgrow-select']) !!}
            </div>
        </div>

        <div style="margin-right: 25px" class="mb-3">
            <div class="xgrow-form-control">
                {!! Form::select('section_id', $sections, null, ['id' => 'section_id', 'class' => 'xgrow-select']) !!}
            </div>
        </div>

        {!! Form::button('Filtrar', ['class'=>'xgrow-button', 'id' => 'filter']) !!}
    </div>
</div>

<form id="form_comments" name="form_comments" method="POST" action="{{route('comments.change_status_selected')}}">
    {{ csrf_field() }}
    {!! Form::hidden('status', $approved) !!}
    <div class="userComments pt-3">

    </div>
</form>
