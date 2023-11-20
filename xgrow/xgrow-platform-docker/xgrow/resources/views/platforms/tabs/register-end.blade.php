<div class="tab-pane fade show" id="registerEnd" :class="{'active': activeScreen.toString() === 'registerEnd'}">

    <div class="row pt-5">
        <div class="col-sm-12 col-md-12 d-flex align-items-center gap-4 flex-column">
            <div style="width: 48px">
                <img src="/xgrow-vendor/assets/img/check.svg" alt="Cadastro finalizado">
            </div>
            <h1 style="font-size: 32px; font-weight: bold;">Sua conta foi criada!</h1>
            <p>Use este e-mail e a senha recebida para se autenticar</p>
            <div class="d-flex gap-2 align-items-center user-avatar">
                <img src="/xgrow-vendor/assets/img/avatar.svg" alt="Cadastro finalizado">
                <span>[[personalData.email]]</span>
            </div>
            <p style="max-width:600px; text-align: center">
                Você já pode acessar o sistema da Xgrow e personalizar sua plataforma da melhor forma possível para
                você.
            </p>
        </div>
        <div class="footer col-sm-12 border-top mt-3 py-3 d-flex justify-content-center">
            <button class="xgrow-button-custom" @click="finishRegister">
                Vamos lá!
            </button>
        </div>
    </div>

</div>
