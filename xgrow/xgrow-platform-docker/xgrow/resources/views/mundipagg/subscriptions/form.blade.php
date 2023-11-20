<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Assinatura
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="seller_id">Seller Id</label>
                            <input type="text" class="form-control" id="seller_id" name="seller_id" value="{{ $subscription->seller_id ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="subscription_id">Subscription Id</label>
                            <input type="text" class="form-control" id="subscription_id" name="subscription_id" value="{{ $subscription->subscription->subscription_id ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="create_date">Create Date</label>
                            <input type="text" class="form-control" id="create_date" name="create_date" value="{{ $subscription->create_date ?? '' }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="payment_date">Payment Date</label>
                            <input type="text" class="form-control" id="payment_date" name="payment_date" value="{{ $subscription->payment_date ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" class="form-control" id="status" name="status" value="{{ $subscription->status ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status_details">Status details</label>
                            <input type="text" class="form-control" id="status_details" name="status_details" value="{{ $subscription->status_details ?? '' }}" readonly>
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
                Customer
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="customer_id">Customer Id</label>
                            <input type="text" class="form-control" id="customer_id" name="customer_id" value="{{ $subscription->customer->customer_id ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">First name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $subscription->customer->first_name ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="last_name">Last name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $subscription->customer->last_name ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="document_type">Document Type</label>
                            <input type="text" class="form-control" id="document_type" name="document_type" value="{{ $subscription->customer->document_type ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="document_number">Document Type</label>
                            <input type="text" class="form-control" id="document_number" name="document_number" value="{{ $subscription->customer->document_number ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="phone_number">Phone number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $subscription->customer->phone_number ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="celphone_number">Cel phone number</label>
                            <input type="text" class="form-control" id="celphone_number" name="celphone_number" value="{{ $subscription->customer->celphone_number ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="observation">Observation</label>
                            <input type="text" class="form-control" id="observation" name="observation" value="{{ $subscription->customer->observation ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" value="{{ $subscription->customer->email ?? '' }}" readonly>
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
                Plan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="plan_id">Plan Id</label>
                            <input type="text" class="form-control" id="plan_id" name="plan_id" value="{{ $subscription->plan->plan_id ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $subscription->plan->name ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="{{ $subscription->plan->description ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="amount">Amount</label>
                            <input type="text" class="form-control" id="amount" name="amount" value="{{ $subscription->plan->amount ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <input type="text" class="form-control" id="currency" name="currency" value="{{ $subscription->plan->currency ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="currency">Payment types</label>
                            @forelse($subscription->plan->payment_types as $item)
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
                            <input type="text" class="form-control" id="sales_tax" name="sales_tax" value="{{ $subscription->plan->sales_tax ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="product_type">Product type</label>
                            <input type="text" class="form-control" id="product_type" name="product_type" value="{{ $subscription->plan->product_type ?? '' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="text" class="form-control" id="status" name="status" value="{{ $subscription->plan->status ?? '' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="period">Period</label>
                            <input type="text" class="form-control" id="period" name="period" value="{ Type: {{ $subscription->plan->period->type ?? '' }}, billing_cycle: {{ $subscription->plan->period->billing_cycle ?? '' }}, specific_cycle_in_days: {{ $subscription->plan->period->specific_cycle_in_days ?? '' }} }" readonly>
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
                Projeção: {{ number_format(substr($projection->amount, 0, -2) . '.' . substr($projection->amount, -2), 2, ',', '.')}}

            </div>
            <div class="card-body">
                <ul class="list-group">
                    @forelse($projection->projection as $item)
                        <li class="list-group-item">{{ substr($item->date,8).'/'.substr($item->date,5,2).'/'.substr($item->date,0,4) }}: {{  number_format(substr($item->amount, 0, -2) . '.' . substr($item->amount, -2), 2, ',', '.')  }}</li>
                    @empty
                        <li>-</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Cancelar assinatura
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('getnet.subscriptions.cancel') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="seller_id">Plan Id</label>
                                <input type="text" class="form-control" id="seller_id" name="seller_id" value="{{ $subscription->seller_id ?? '' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="status_details">Motivo *</label>
                                <input type="text" class="form-control" id="status_details" name="status_details" value="" required>
                                <input type="hidden" class="form-control" id="subscription_id" name="subscription_id" value="{{ $subscription->subscription->subscription_id ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-danger">Cancelar assinatura</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


