<div class="row">
    <div class="col-md-5">
        <div class="form-group">
            {!! Form::label('name','Nome:') !!}
            {!! Form::text('name', (isset($config->name) ? $config->name : ''), [ 'class' =>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('email','E-mail:') !!}
            {!! Form::text('email', (isset($config->email) ? $config->email : ''), [ 'class' =>'form-control']) !!}
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('document','CNPJ:') !!}
            {!! Form::text('document', (isset($config->document) ? $config->document : ''), [ 'class' =>'form-control']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('bank','Banco:') !!}
            {!! Form::text('bank', (isset($config->bank) ? $config->bank : ''), [ 'class' =>'form-control', 'maxlength' => '10']) !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('branch','Agência:') !!}
            {!! Form::text('branch', (isset($config->branch) ? $config->branch : ''), [ 'class' =>'form-control', 'maxlength' => '10']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('account','Conta:') !!}
            {!! Form::text('account', (isset($config->account) ? $config->account : ''), [ 'class' =>'form-control', 'maxlength' => '15']) !!}
        </div>
    </div>
</div>
