<div class="tab-pane fade show" id="personalData" :class="{'active': activeScreen.toString() === 'personalData'}">
    <div class="row">
        <div class="col-12 pt-3 pb-4">
            <p class="xgrow-panel-subtitle">Cadastre-se manualmente</p>
        </div>

        <form autocomplete="off" data-grecaptcha-action="message">
            <div class="col-sm-12">
                <div class="group">
                    {!! Form::text('name', null, ['id' => 'name', 'placeholder' => 'Insira seu nome completo...', 'v-model' => 'personalData.name']) !!}
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    {!! Form::label('name', 'Nome completo') !!}
                </div>
            </div>

            <div class="col-sm-12">
                <div class="group">
                    {!! Form::email('email', null, ['id' => 'email', 'placeholder' => 'Insira seu e-mail de acesso...', 'v-model' => 'personalData.email']) !!}
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    {!! Form::label('email', 'E-mail') !!}
                </div>
            </div>

            <div class="col-sm-12">
                <div class="group">
                    {!! Form::email('confirmEmail', null, ['id' => 'confirmEmail', 'placeholder' => 'Insira novamente seu e-mail de acesso...', 'v-model' => 'personalData.confirmEmail']) !!}
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    {!! Form::label('confirmEmail', 'Confirme seu e-mail') !!}
                </div>
            </div>

            <div class="col-sm-12">
                <div class="group">
                    {!! Form::text('phone', null, ['id' => 'phone', 'placeholder' => 'Insira seu número de telefone...', 'v-maska' => '"(##)#####-####"', 'v-model' => 'personalData.phone']) !!}
                    <span class="highlight"></span>
                    <span class="bar"></span>
                    {!! Form::label('phone', 'Telefone') !!}
                </div>
            </div>
        </form>
        <div class="footer col-sm-12 border-top py-3 text-end">
            <button id="confirm-data-button" class="xgrow-button-custom" @click.prevent="nextStep('termsAndConditions')">
                Confirmar e prosseguir
            </button>
        </div>
    </div>

</div>
