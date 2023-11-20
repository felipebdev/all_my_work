<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Plano
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="plan_id">Plan Id</label>
                            <input type="text" class="form-control" id="plan_id" name="plan_id" value="{{ $plan->plan_id ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $plan->name ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ $plan->description ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" value="{{ $plan->amount ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <input type="text" class="form-control" id="currency" name="currency" value="{{ $plan->currency ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="currency">Payment types</label>
                            @forelse($plan->payment_types as $item)
                                <input type="text" class="form-control" id="currency" name="currency" value="{{ $item ?? '' }}" readonly>
                            @empty
                                <p class="card-text">-</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sales_tax">Sales tax</label>
                            <input type="text" class="form-control" id="sales_tax" name="sales_tax" value="{{ $plan->sales_tax ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_type">Product type</label>
                            <input type="text" class="form-control" id="product_type" name="product_type" value="{{ $plan->product_type ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" class="form-control" id="status" name="status" value="{{ $plan->status ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="period">Period</label>
                            <input type="text" class="form-control" id="period" name="period" value="{ type: {{ $plan->period->type ?? '' }}, billing_cycle: {{ $plan->period->billing_cycle ?? '' }}, specific_cycle_in_days: {{ $plan->period->specific_cycle_in_days ?? '' }} }" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Atualizar dados do plano
            </div>
            <div class="card-body">
                <form method="POST" action="{{ url('/getnet/plans/'.$plan->plan_id) }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name">Name *</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $plan->name ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <input type="text" class="form-control" id="description" name="description" value="{{ $plan->description ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Atualizar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
