<template>
    <confirm-modal :isOpen="isOpen" modalSize="lg" class="modal">
        <template v-slot:header>
            <div class="modal__header">
                <h1 class="modal__title">
                    {{ isEdit ? "Editar" : "Adicionar" }} conteúdo:
                    <span class="modal__title modal__title--semi-bold">
                        Título
                    </span>
                </h1>
            </div>
        </template>
        <div class="modal__body">
            <p class="modal__text">Selecione o tamanho do texto e insira abaixo o título do seu conteúdo.</p>

            <select-title
                :options="options"
                id="selectTitle"
                placeholder="Selecione um tipo de título"
                v-model="selected"
            />

            <input-vue
                class="modal__input text-start"
                id="title"
                label="Título do conteúdo"
                v-model="title"
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
import SelectTitle from '../components/SelectTitle.vue';

export default {
    name: 'title-modal',
    components: {
        ConfirmModal,
        DefaultButton,
        InputVue,
        SelectTitle
    },
    data() {
        return {
            title: "",
            options: [
                { value: 'h1', name: 'Título 1' },
                { value: 'h2', name: 'Título 2' },
                { value: 'h3', name: 'Título 3' },
            ],
            selected: { value: 'h2', name: 'Título 2' }
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
                        text_type: this.selected.value,
                        type: this.data.type,
                        text: this.title,
                    });
                }
                else {
                    successToast("Ação realizada", "Widget adicionado com sucesso");

                    this.$emit('save', {
                        text_type: this.selected.value,
                        text: this.title,
                    });
                }
            } catch (message) {
                errorToast("Não foi possível realizar a ação", message)
            }
        },
        preloadSelectOption(type) {
            const titles = {
                h1: { value: 'h1', name: 'Título 1' },
                h2: { value: 'h2', name: 'Título 2' },
                h3: { value: 'h3', name: 'Título 3' },
            }

            return titles[type];
        },
        validation() {
            if (this.title == undefined || this.title == "")
                throw new Error("O campo título é obrigatório");

        }
    },
    props: {
        data: {
            type: Object,
            default: {
                text_type: "h2",
                text: "",
            }
        },
        isEdit: { type: Boolean, required: true },
        isOpen: { type: Boolean, required: true },
    },
    watch: {
        data(newState) {
            this.selected = this.preloadSelectOption(newState.text_type);
            this.title = newState.text;
        }
    },
    mounted() {
        this.selected = this.preloadSelectOption(this.data.text_type);
        this.title = this.data.text;
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
