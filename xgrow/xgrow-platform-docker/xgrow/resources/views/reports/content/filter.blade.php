<div class="col-md-12">
    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label mb-0">
        <input type="text" value="{{ $search['period'] ?? '' }}" class="form-control" name="daterange" id="reportrange"
            style="border:none;
               outline:none;
               background-color: var(--input-bg);
               border-bottom: 1px solid var(--border-color);
               box-shadow: none;
               min-width: 230px;
               color: var(--contrast-green)" autocomplete="off">
        <label for="daterange">Filtrar por data</label>
    </div>
</div>
