<div class="xgrow-btn-group btn-group" role="group" aria-label="Basic radio toggle button group">
    <input type="radio" class="btn-check logical-and-condition"
           id="logical-and-condition__{{$uniq ?? '0'}}"
           name="logical-condition__{{$uniq ?? '0'}}"
           value="1"
           {{ ($value ?? 0) == 1 ? 'checked' : '' }}
    >
    <label class="btn btn-outline-primary" for="logical-and-condition__{{$uniq ?? '0'}}">E</label>

    <input type="radio" class="btn-check logical-or-condition"
           id="logical-or-condition__{{$uniq ?? '0'}}"
           name="logical-condition__{{$uniq ?? '0'}}"
           value="2"
           {{ ($value ?? 0) == 2 ? 'checked' :'' }}
    >
    <label class="btn btn-outline-primary" for="logical-or-condition__{{$uniq ?? '0'}}">OU</label>
</div>
