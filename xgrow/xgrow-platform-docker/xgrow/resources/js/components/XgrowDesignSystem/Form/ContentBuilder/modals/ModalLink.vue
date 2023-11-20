<template>
    <confirm-modal :isOpen="isOpen" modalSize="lg" class="modal">
        <template v-slot:header>
            <div class="modal__header">
                <h1 class="modal__title">
                    {{ isEdit ? "Editar" : "Adicionar" }} conteúdo:
                    <span class="modal__title modal__title--semi-bold">
                        Link externo
                    </span>
                </h1>
            </div>
        </template>
        <div class="modal__body">
            <p class="modal__text">Insira a URL do link externo.</p>

            <switch-button class="d-flex gap-3" id="oauthToken" v-model="useOAuthToken">
                Usar o OAuth token
            </switch-button>

            <input-vue
                class="modal__input"
                id="title_url"
                label="Título"
                placeholder="Insira o título do link externo..."
                v-model="title"
            />

            <input-vue
                class="modal__input"
                id="url"
                label="URL do link externo"
                placeholder="https://"
                v-model="url"
            />

            <div class="modal__actions">
                <default-button
                    :onClick="close"
                    :outline="true"
                    text="Voltar"
                />
                <default-button
                    :onClick="save"
                    icon="fas fa-save"
                    status="success"
                    text="Salvar"
                />
            </div>
        </div>
    </confirm-modal>
</template>

<script>
import DefaultButton from '../../../Buttons/DefaultButton.vue';
import ConfirmModal from '../../../Modals/ConfirmModal.vue';
import InputVue from '../../Input.vue';
import SwitchButton from '../../SwitchButton.vue';

export default {
    name: 'link-modal',
    components: {
        ConfirmModal,
        InputVue,
        DefaultButton,
        SwitchButton
    },
    data() {
        return {
            title: "",
            url: "",
            useOAuthToken: false
        }
    },
    methods: {
        close() { this.$emit('close'); },
        save() {
            try {
                this.validation();

                if (this.isEdit) {
                    successToast("Ação realizada", "Widget atualizado com sucesso");

                    this.$emit('update', {
                        position: this.data.position,
                        type: this.data.type,
                        use_oauth_external_token: this.useOAuthToken,
                        external_link_title: this.title,
                        external_link_url: this.url,
                    });
                }
                else {
                    successToast("Ação realizada", "Widget adicionado com sucesso");

                    this.$emit('save', {
                        type: this.data.type,
                        external_link_url: this.url,
                        external_link_title: this.title,
                        use_oauth_external_token: this.useOAuthToken
                     });
                }
            }
            catch(message) {
                errorToast("Não foi possível realizar a operação", message);
            }
        },
        validation() {
            if(!this.url)
                throw new Error("O campo URL do link externo é obrigatório");

            if(this.title == "")
                throw new Error("O campo título é obrigatório");

            if(this.url && !this.url.includes("https://"))
                throw new Error("O link externo deve conter o protocolo https");
        }
    },
    props: {
        data: { type: Object, default: { url: "", title: "", use_oauth_external_token: false, external_link_title: "" } },
        isEdit: { type: Boolean, required: true },
        isOpen: { type: Boolean, required: true },
    },
    mounted() {
        this.url = this.data.external_link_url;
        this.title = this.data.external_link_title;
        this.useOAuthToken = this.data.use_oauth_external_token;
    }
}
</script>

<style lang="scss" scoped>
    .modal {
        &__header {
            border-bottom: 1px solid rgba(#C4C4C4, .15);
            padding: 20px 0;
            margin-left: 12px;
            width: 100%;
        }

        &__title {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.6;

            &--semi-bold { font-weight: 600; }
        }

        &__body {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 16px;
            width: 100%;
        }

        &__text {
            font-size: 16px;
            font-weight: 600;
            line-height: 1.6;
            text-align: left;
        }

        &__actions {
            border-top: 1px solid rgba(#C4C4C4, .15);
            display: flex;
            gap: 20px;
            justify-content: flex-end;
            padding-top: 40px;
        }
    }
</style>
