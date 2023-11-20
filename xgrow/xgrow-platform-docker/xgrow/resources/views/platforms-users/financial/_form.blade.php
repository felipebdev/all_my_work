@php
    $states = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
@endphp

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="row">
            <p class="mb-3">Dados pessoais</p>
            <div class="col-12 mb-3">
                <small>O tipo de pessoa e documento não podem ser alterados futuramente, preste atenção ao
                    preencher.</small>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                    <select class="xgrow-select" name="type_person" id="type_person" tabindex="1"
                        {{ isset($client->type_person) ? 'disabled' : '' }}>
                        <option value="F" {{ $client->type_person == 'F' ? 'selected' : '' }}>Pessoa Física
                        </option>
                        <option value="J" {{ $client->type_person == 'J' ? 'selected' : '' }}>Pessoa Jurídica
                        </option>
                    </select>
                    <label for="">Tipo de pessoa</label>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="document" name="document"
                        tabindex="2" value="{{ $client->cpf ?? $client->cnpj }}" required
                        {{ isset($client->cpf) || isset($client->cnpj) ? 'disabled' : '' }}>
                    <label id="document_label">{{ $client->type_person == 'J' ? 'CNPJ' : 'CPF' }}</label>
                </div>
            </div>
            @if (isset($client->type_person) && (isset($client->cpf) || isset($client->cnpj)))
                <input type="hidden" name="type_person" value="{{ $client->type_person }}">
                <input type="hidden" name="document" value="{{ $client->cpf ?? $client->cnpj }}">
            @endif
            <div class="col-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="phone_number" name="phone_number"
                        tabindex="3" value="{{ $client->phone_number }}" {{ $isOwner ? 'required' : 'disabled' }}>
                    <label>Telefone</label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-md-6 col-sm-12">
                <div class="row">
                    <p class="mb-3">Informações da conta</p>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input autocomplete="off" type="text" spellcheck="false" id="holder_name"
                                name="holder_name" tabindex="4" value="{{ $bankAccount->holder_name }}"
                                {{ $isOwner && $platform->recipient_id === null ? 'required' : 'disabled' }}>
                            <label>Nome do titular</label>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                            <div class="xgrow-form-control">
                                <select name="bank" id="bank" class="xgrow-select-tag" style="width: 100%"
                                    placeholder="Banco"
                                    {{ $isOwner && $platform->recipient_id === null ? 'required' : 'disabled' }}>
                                    <option value=""></option>
                                    @foreach ($bankList as $item)
                                        <option value="{{ $item->code }}"
                                            {{ $item->code == $bankAccount->bank ? 'selected' : '' }}>
                                            {{ $item->code }} - {{ $item->bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                            <select class="xgrow-select" name="account_type" id="account_type" tabindex="7"
                                {{ $isOwner && $platform->recipient_id === null ? 'required' : 'disabled' }}>
                                <option value="checking"
                                    {{ $bankAccount->account_type == 'checking' ? 'selected' : '' }}>
                                    Corrente
                                </option>
                                <option value="savings"
                                    {{ $bankAccount->account_type == 'savings' ? 'selected' : '' }}>
                                    Poupança
                                </option>
                            </select>
                            <label for="">Tipo de conta</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input autocomplete="off" type="text" spellcheck="false" id="branch" name="branch"
                                tabindex="7" value="{{ $bankAccount->branch }}"
                                {{ $isOwner && $platform->recipient_id === null ? 'required' : 'disabled' }}>
                            <label>Agência</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input autocomplete="off" type="text" spellcheck="false" id="branch_check_digit"
                                name="branch_check_digit" tabindex="8" value="{{ $bankAccount->branch_check_digit }}"
                                {{ $isOwner && $platform->recipient_id === null ? '' : 'disabled' }}>
                            <label>DG</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input autocomplete="off" type="number" spellcheck="false" id="account" name="account"
                                tabindex="9" maxlength="12" value="{{ $bankAccount->account }}"
                                {{ $isOwner && $platform->recipient_id === null ? 'required' : 'disabled' }}>
                            <label>Conta</label>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                            <input autocomplete="off" type="number" spellcheck="false" id="account_check_digit"
                                name="account_check_digit" tabindex="10" maxlength="1"
                                value="{{ $bankAccount->account_check_digit }}"
                                {{ $isOwner && $platform->recipient_id === null ? 'required' : 'disabled' }}>
                            <label>DG</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <p class="mb-3">Documento:</p>
                <div class="d-flex align-items-center justify-content-center">
                    <div>
                        <img src="{{ $client->document_front_image_url }}" alt="Documento enviado" class="w-100"
                            style="max-height: 256px !important;"
                            onerror="this.src='https://las.xgrow.com/background-default.png'">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <p class="my-3">Taxas</p>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card-info">
                    <b>Taxa por transação</b>
                    <p>R$ {{ number_format($client->tax_transaction, 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card-info">
                    <b>Taxa da plataforma</b>
                    <p>{{ number_format(100 - $client->percent_split, 2, ',', '.') }}%</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <p class="my-3">Endereço</p>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="zipcode" name="zipcode"
                        tabindex="11" value="{{ $client->zipcode }}" {{ $isOwner ? 'required' : 'disabled' }}>
                    <label>CEP</label>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="address" name="address"
                        tabindex="12" value="{{ $client->address }}" {{ $isOwner ? 'required' : 'disabled' }}>
                    <label>Rua</label>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="number" name="number"
                        tabindex="13" value="{{ $client->number }}" {{ $isOwner ? 'required' : 'disabled' }}>
                    <label>Nº</label>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="district" name="district"
                        tabindex="14" value="{{ $client->district }}" {{ $isOwner ? 'required' : 'disabled' }}>
                    <label>Bairro</label>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="city" name="city"
                        tabindex="15" value="{{ $client->city }}" {{ $isOwner ? 'required' : 'disabled' }}>
                    <label>Cidade</label>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12">
                <div class="xgrow-form-control mui-textfield mui-textfield--float-label mb-3">
                    <select class="xgrow-select" name="state" id="state" tabindex="16"
                        {{ $isOwner ? 'required' : 'disabled' }}>
                        @foreach ($states as $state)
                            <option value="{{ $state }}" {{ $client->state == $state ? 'selected' : '' }}>
                                {{ $state }}</option>
                        @endforeach
                    </select>
                    <label for="">UF</label>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                    <input autocomplete="off" type="text" spellcheck="false" id="complement" name="complement"
                        tabindex="17" value="{{ $client->complement }}" {{ $isOwner ? '' : 'disabled' }}>
                    <label>Complemento</label>
                </div>
            </div>
        </div>
    </div>
</div>
{{ csrf_field() }}
{{ method_field('PUT') }}

<style>
    .xgrow-floating-input input {
        min-width: fit-content !important;
    }

    .card-info {
        border: 1px solid var(--green1);
        border-radius: 4px;
        padding: 1rem 0.5rem;
        align-items: center;
        text-align: center;
    }
</style>
