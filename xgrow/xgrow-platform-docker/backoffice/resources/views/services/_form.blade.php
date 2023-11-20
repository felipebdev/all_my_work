@push('after-scripts')
    <script>
        $(document).ready(function () {
            const price = parseFloat($("#ipt-price").val()).toFixed(2);
            $("#ipt-price").val(price);
            $('#ipt-price').mask('##0.00', {reverse: true});  
            
            $('#txta-description').summernote({
                height: 300,
                minHeight: null,
                maxHeight: null,
                focus: false,
                toolbar: [
                    ['para', ['ul']],
                    ['view', ['codeview']]
                ]
            });
        });
    </script>
@endpush

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="user_email">Nome*</label>
            <input type="text" class="form-control" name="name"
                value="{{ old('name', $service->name ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="customer_id">Tipo*</label>
            <select class="form-control" name="type" required>
                <option></option>
                <option value="plan" {{ old('type', $service->type ?? '') === 'plan' ? 'selected' : '' }}>Plano</option>
                <option value="addon" {{ old('type', $service->type ?? '') === 'addon' ? 'selected' : '' }}>Addon</option>
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="user_email">Preço*</label>
            <input type="text" id="ipt-price" class="form-control" name="price"
                value="{{ old('price', $service->price ?? '') }}" required>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="description">Descrição*</label>
            <textarea name="description" id="txta-description" class="content_html form-control" cols="10" rows="3" required>{{ old('description', $service->description ?? '') }}</textarea>
        </div>
    </div>
</div>
