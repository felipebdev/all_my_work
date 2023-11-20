<template>
    <div class='bg' :style="isLoading ? 'display:block' : 'display:none'">
        <div class='modal-sections modal' tabindex='-1' data-bs-backdrop='static' data-bs-keyboard='false'
            :style="isLoading ? 'display:block' : 'display:none'">
            <div class='modal-dialog modal-dialog-centered modal-md'>
                <div class='modal-content bg-black-80'>
                    <div class='modal-header'>
                        <button type='button' data-bs-dismiss='modal' aria-label='Close' @click='setLoading(false)'
                            class="d-none">
                            <img src='/xgrow-vendor/assets/img/logo/symbol.svg' />
                        </button>
                    </div>
                    <div class='modal-body'>
                        <img src='/xgrow-vendor/assets/img/logo/dark.svg' :alt="text" />
                        <h5><span v-html='text'></span></h5>
                        <template v-if='icon'>
                            <i :class="`fa ${icon} fa-7x fa-spin`"></i>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { mapActions, mapState, mapStores } from "pinia";
import { useLoadingStore } from "../../../store/components/loading";

export default {
    name: 'LoadingStore',
    props: {
        status: { type: String, default: 'loading' }
    },
    computed: {
        ...mapStores(useLoadingStore),
        ...mapState(useLoadingStore, ['isLoading']),
        /** Icon used for loading */
        icon: function () {
            if (['loading', 'saving', 'newPlatform'].includes(this.status))
                return 'fa-circle-notch'
            if (this.status === 'success')
                return 'fa-circle-notch'
            if (this.status === 'error')
                return 'fa-circle-notch'
        },
        /** Text used for loading */
        text: function () {
            if (this.status === 'loading')
                return 'Aguarde, estamos carregando as<br>informações...'
            if (this.status === 'saving')
                return 'Aguarde enquanto salvamos<br>as informações...'
            if (this.status === 'success')
                return 'Informações salvas com sucesso!'
            if (this.status === 'error')
                return 'Ocorreu algum problema ao salvar as<br>informações, tente novamente mais tarde.'
            if (this.status === 'creatingPlatform')
                return '<img src="/xgrow-vendor/assets/img/icons/dashboard.svg" class="mt-3" alt="Criando sua plataforma"/><br><b>Criando sua plataforma</b><br>Estamos preparando tudo para você'
        },
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
    },
}
</script>

<style lang='scss' scoped>
.bg {
    background: rgba(0, 0, 0, 0.5);
    width: 100%;
    height: 100%;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 999999;
    display: block;
}

.modal-header {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    border: none;
    height: 32px;

    button {
        cursor: pointer;
        background: transparent;
        border: none;

        img {
            height: 22px;
        }
    }
}

.modal-body {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    color: #FFFFFF;
    gap: 1rem;

    img {
        height: 48px;
    }
}

.modal-content {
    max-width: 100% !important;
    padding: 0 0 65px 0;
}

@media (min-width: 992px) {
    .modal-md {
        max-width: 600px;
    }
}
</style>
