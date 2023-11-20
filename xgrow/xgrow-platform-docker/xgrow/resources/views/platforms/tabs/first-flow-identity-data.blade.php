<div class="tab-pane fade show" id="dataPerson" :class="{'active': activeScreen === 'dataPerson'}">

    <div class="row">
        <div class="col-sm-12 col-md-12 text-center">
            <p class="pb-2"><b>Informe seus dados de identificação como pessoa física.</b></p>
        </div>

        <div class="col-sm-12 col-md-12">
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-circle"></i> <span>Estes dados não poderão ser alterados mais tarde.</span>
            </div>
            <div class="row xgrow-inner-card">
                <div class="col-sm-12 col-md-12">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('name', null, ['id' => 'name', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-touched mui--is-dirty mui--is-not-empty', 'v-model' => 'user.name']) !!}
                        {!! Form::label('name', 'Nome completo') !!}
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::email('email', null, ['id' => 'email', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-touched mui--is-dirty mui--is-not-empty', 'v-model' => 'user.email', 'disabled']) !!}
                        {!! Form::label('email', 'E-mail') !!}
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="xgrow-floating-input mui-textfield mui-textfield--float-label">
                        {!! Form::text('identity', null, ['id' => 'identity', 'v-maska' => 'user.type === "F" ? "###.###.###-##" : "##.###.###/####-##"', 'autocomplete' => 'off', 'spellcheck' => 'false', 'class' => 'mui--is-empty mui--is-untouched mui--is-pristine', 'v-model' => 'user.identity', ':placeholder' => 'user.type === "F" ? "Insira seu CPF..." : "Insira seu CNPJ..."']) !!}
                        {!! Form::label('identity', "[[user.type === 'F' ? 'CPF' : 'CNPJ']]") !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="footer col-sm-12 border-top mt-3 py-3 d-flex justify-content-center gap-2">
            <button class="xgrow-button xgrow-button-custom-secondary" @click="previousStep('typePerson')">
                Voltar
            </button>
            <button class="xgrow-button xgrow-button-custom" @click="openConfirmModal">
                Confirmar e prosseguir
            </button>
        </div>
    </div>

</div>

<xgrow-modal-component :is-open="confirmModal" @close="confirmModal=false">
    <template v-slot:content>
        <div class="row gap-3 text-center w-100" style="color:var(--gray1)">
            <i aria-hidden="true" class="fas custom-alert-symbol fa-exclamation-circle fa-5x"></i>
            <h5 class="m-0 p-0" style="color:#FFFFFF"><b>Deseja realmente continuar?</b></h5>
            <span><b>Estes dados não poderão ser alterados mais tarde.</b></span>
            <p><b>Nome completo:</b> [[ user.name]]</p>
            <p><b>E-mail:</b> [[ user.email]]</p>
            <p><b>Documento:</b> [[ user.identity]]</p>
        </div>
    </template>
    <template v-slot:footer="slotProps" style="justify-content: center!important">
        <button type="button" class="btn btn-outline-light mr-2 xgrow-button-cancel" @click="slotProps.closeModal">
            Voltar
        </button>
        <button type="button" class="btn btn-success" @click.prevent="save">
            <i class="fas fa-check mr-2"></i> Sim, confirmar
        </button>
    </template>
</xgrow-modal-component>
