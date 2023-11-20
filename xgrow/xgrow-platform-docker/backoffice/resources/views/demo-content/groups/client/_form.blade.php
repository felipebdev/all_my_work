@push('after-scripts')
    <script>
        $(document).ready(
            function () {
                $('input[name=type_person]').click(
                    function () {
                        if ($(this).val() === 'F') {
                            $('#row_cnpj').addClass('d-none');
                            $('#row_cpf').removeClass('d-none');
                        } else {
                            $('#row_cpf').addClass('d-none');
                            $('#row_cnpj').removeClass('d-none');
                        }
                    }
                );

                $('#change_password').click(
                    function () {
                        if ($(this).prop('checked'))
                            $('#row_password').removeClass('d-none');
                        else
                            $('#row_password').addClass('d-none');
                    }
                );

                $('#zipcode').mask('99999-999');
                $('#cpf').mask('999.999.999-99');
                $('#cnpj').mask('99.999.999/9999-99');

                const taxTransaction = parseFloat($("#tax_transaction").val()).toFixed(2);
                $("#tax_transaction").val(taxTransaction);
                $('#tax_transaction').mask('##0.00', {reverse: true});
            }
        );

        $(function () {
            var campo = $(".email");
            campo.keyup(function (e) {
                e.preventDefault();
                campo.val($(this).val().toLowerCase());
            });
        });
    </script>
@endpush

@push('after-styles')
    <link rel="stylesheet" href="{{asset('vendor/password-validator/password-validator.css')}}">
@endpush

@push('after-scripts')
    <script src="{{asset('vendor/password-validator/password-validator.js')}}"></script>
@endpush

@if (count($errors) > 0)
    <div class="row">
        <div class="col col-sm-6 offset-sm-3">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        {{ $error }} <br/>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

@if($client->id > 0)
    {!! Form::model($client,['method'=>'put', 'enctype' => 'multipart/form-data', 'route'=>['client.update', $client->id]]) !!}
@else
    {!! Form::model($client,['method' => 'post', 'enctype' => 'multipart/form-data', 'route'=>'client.store']) !!}
@endif

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('first_name','*Primeiro nome:') !!}
            {!! Form::text('first_name', null, ['class'=>'form-control', 'required']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('last_name','*Último nome:') !!}
            {!! Form::text('last_name', null, ['class'=>'form-control', 'required']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('email','*Email:') !!}
            {!! Form::email('email', null, ['class'=>'form-control email', 'maxlength' => '60', 'style'=>'text-transform:lowercase;', 'required']) !!}
        </div>
    </div>
</div>

<div class="row mb-3 @if($client->id == 0) d-none @endif">
    <div class="col col-md-6">
        <input type="checkbox" name="change_password" id="change_password"> Alterar senha
    </div>
</div>

<div class="row @if($client->id > 0) d-none @endif" id="row_password">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('password','*Senha:') !!}
            {!! Form::password('password', ['class'=>'form-control']) !!}
        </div>
        <div class="password-policies">
            <div class="policy-length">
                5 caracteres.
            </div>
            <div class="policy-number">
                Contém números.
            </div>
            <div class="policy-letter">
                Contém letras.
            </div>
            <div class="policy-special">
                Contém caracteres especiais.
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('password_confirmation','*Confirmação da Senha:') !!}
            {!! Form::password('password_confirmation', ['class'=>'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label for="email">*Tipo pessoa:</label><br/>
            {!! Form::radio('type_person', 'F', ['id' => 'type_person_F']) !!} Física
            {!! Form::radio('type_person', 'J', ['id' => 'type_person_J']) !!} Jurídica
        </div>
    </div>

    <div class="col-md-6
	    	@if($client->type_person == 'J' or old("type_person")=='J')
        d-none
@endif
        " id="row_cpf">
        <div class="form-group">
            {!! Form::label('cpf','*CPF:') !!}
            {!! Form::text('cpf', null, ['class' => 'form-control', 'maxlength' => '30']) !!}
        </div>
    </div>

    <div class="col-md-6
	    	@if(old("type_person")=='F' or $client->type_person == 'F')
        d-none
@endif" id="row_cnpj">
        <div class="form-group">
            {!! Form::label('cnpj','*CNPJ:') !!}
            {!! Form::text('cnpj', null, ['class'=>'form-control', 'maxlength' => '30']) !!}
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="file">Foto:</label>
            <input type="file">
        </div>
    </div>
</div>

<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('fantasy_name','*Nome da empresa:') !!}
            {!! Form::text('fantasy_name', null, ['class'=>'form-control','required']) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('company_name','*Razão Social:') !!}
            {!! Form::text('company_name',
                              null,
                              [
                              'class'     =>'form-control',
                              'required'
                               ]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('company_url','URL da empresa:') !!}
            {!! Form::url('company_url',
                              null,
                              [
                              'class'     =>'form-control'
                               ]) !!}

        </div>
    </div>

</div>


<div class="row">

    <div class="col-md-6">
        <div class='form-group'>
            {!! Form::label('zipcode','*CEP:') !!}
            {!! Form::text('zipcode',
                      null,
                      [
                      'class'     =>'form-control zipcode',
                      'maxlength' => '20',
                      'onchange' => 'consulta_cep()',
                      'required'
                       ]) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class='form-group '>
            {!! Form::label('address','*Endereço:') !!}
            {!! Form::text('address',
                    null,
                    [
                    'class'    =>'form-control',
                    'maxlength'    => '60',
                    'required'
                    ]) !!}
        </div>
    </div>

</div>


<div class="row">

    <div class="col-md-4">
        <div class='form-group'>
            {!! Form::label('number','*Número:') !!}
            {!! Form::text('number',
                  null,
                  [
                  'class'    =>'form-control',
                  'maxlength' => '10',
                  'required'
                  ]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class='form-group'>
            {!! Form::label('complement','Complemento:') !!}
            {!! Form::text('complement',
                    null,
                    [
                    'class'    =>'form-control',
                    'maxlength' => '20'
                    ]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class='form-group'>
            {!! Form::label('district','*Bairro:') !!}
            {!! Form::text('district',
                       null,
                       [
                       'class'    =>'form-control',
                       'maxlength' => '30',
                       'required'
                       ]) !!}
        </div>
    </div>

</div>


<div class="row">

    <div class="col-md-6">
        <div class='form-group'>
            {!! Form::label('state','*Estado:') !!}
            {!! Form::select('state', array('' => '') + $states, null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

    <div class="col-md-6">
        <div class='form-group '>
            {!! Form::label('city','*Cidade:') !!}
            {!! Form::text('city', null, ['class' => 'form-control', 'required' => 'required']) !!}
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('percent_split','Percentual a receber:') !!}
            {!! Form::text('percent_split', old('tax_transaction', $client->percent_split ?? 94.01), [ 'class' =>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('tax_transaction','Taxa fixa por venda:') !!}
            {!! Form::text('tax_transaction', old('tax_transaction', $client->tax_transaction ?? 2), [ 'class' =>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group" style="margin-top: 30px">
            {!! Form::checkbox('is_default_antecipation_tax', true) !!}
            {!! Form::label('is_default_antecipation_tax','Descontar taxa de antecipação com o valor padrão') !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('bank','Banco:') !!}
            {!! Form::text('bank', null, [ 'class' =>'form-control', 'maxlength' => '10']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('branch','Agência:') !!}
            {!! Form::text('branch', null, [ 'class' =>'form-control', 'maxlength' => '6']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('account','Conta:') !!}
            {!! Form::text('account', null, [ 'class' =>'form-control', 'maxlength' => '15']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('recipient_id','Código do recebedor (gerado automaticamente):') !!}
            {!! Form::text('recipient_id', null, [ 'class' =>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('statement_descriptor','Descrição exibida na fatura do cartão:') !!}
            {!! Form::text('statement_descriptor', null, [ 'class' =>'form-control', 'maxlength'=>13]) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class='form-group'>
            {!! Form::submit('Salvar',['class'=>'btn btn-primary btn-primary-custom']) !!}
        </div>
    </div>
</div>

{!! Form::close() !!}
