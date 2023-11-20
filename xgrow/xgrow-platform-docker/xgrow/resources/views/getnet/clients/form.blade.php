<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="seller_id">Seller Id</label>
            <input type="text" class="form-control" id="seller_id" name="seller_id" value="{{ $customer->seller_id ?? '' }}" readonly>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="customer_id">Customer Id</label>
            <input type="text" class="form-control" id="customer_id" name="customer_id" value="{{ $customer->customer_id ?? '' }}" readonly>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="first_name">First name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $customer->first_name ?? '' }}" readonly>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="last_name">Last name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $customer->last_name ?? '' }}" readonly>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="document_type">Document type</label>
            <input type="text" class="form-control" id="document_type" name="document_type" value="{{ $customer->document_type ?? '' }}" readonly>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="document_number">Document number</label>
            <input type="text" class="form-control" id="document_number" name="document_number" value="{{ $customer->document_number ?? '' }}" readonly>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="status">Status</label>
            <input type="text" class="form-control" id="status" name="status" value="{{ $customer->status ?? '' }}" readonly>
        </div>
    </div>
</div>
