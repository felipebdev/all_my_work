<template>
    <confirm-modal :isOpen="isOpen" modalSize="lg" class="modal">
        <template v-slot:header>
            <div class="modal__header">
                <h1 class="modal__title">
                    {{ isEdit ? "Editar" : "Adicionar" }} conteúdo:
                    <span class="modal__title modal__title--semi-bold">
                        Texto
                    </span>
                </h1>
            </div>
        </template>
        <div class="modal__body">
            <p class="modal__text">Insira abaixo o texto do seu conteúdo.</p>

            <text-input-vue
                class="modal__input text-start"
                id="text"
                label="Texto do conteúdo"
                v-model="text"
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
import TextInputVue from '../../TextInput.vue';

export default {
    name: 'text-modal',
    components: {
        ConfirmModal,
        TextInputVue,
        DefaultButton
    },
    data() {
        return {
            text: "",
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
                        text: this.text,
                        text_type: this.data.text_type,
                    });
                }
                else {
                    successToast("Ação realizada", "Widget adicionado com sucesso");

                    this.$emit('save', {
                        type: this.data.type,
                        text: this.text,
                        text_type: this.data.text_type,
                    });
                }
            } catch (message) {
                errorToast("Não foi possível realizar a ação", message)
            }

        },
        validation() {
            if (this.text == undefined || this.text == "")
                throw new Error("O campo texto é obrigatório");
        }
    },
    props: {
        data: {
            type: Object,
            default: {
                text_type: "body",
                text: "",
            }
        },
        isEdit: { type: Boolean, required: true },
        isOpen: { type: Boolean, required: true },
    },
    watch: {
        data(newState) {
            this.text = newState.text;
        }
    },
    mounted() {
        this.text = this.data.text;
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
