@if(!@isset($attributes))
    @php
        $attributes = '';
    @endphp
@endif
<div class="row">

    <div class="col">

        <button type="submit" class="btn btn-success" {{$attributes}}>
            <span class="submit-content">{{$label}}</span>
            <span class="submit-spinner spinner-border spinner-border-sm hidden" role="status" aria-hidden="true"></span>
        </button>

    </div>

</div>
