<template>
    <confirm-modal :isOpen="isOpen" modalSize="lg" class="modal">
        <template v-slot:header>
            <div class="modal__header">
                <h1 class="modal__title">
                    {{ isEdit ? "Editar" : "Adicionar" }} conteúdo:
                    <span class="modal__title modal__title--semi-bold">
                        Imagem
                    </span>
                </h1>
            </div>
        </template>
        <div class="modal__body">
            <p class="modal__text">Clique no botão abaixo para adicionar uma imagem.</p>

            <div class="modal__input-wrapper">
                <file-input-vue
                    id="imageInput"
                    refer="imageInput"
                    ref="imageInput"
                    accept=".png,.jpg,.jpeg"
                    label="Escolha um arquivo"
                    @sendFile="getFile"
                />
            </div>

            <p class="modal__text" v-if="file">
                <a class="modal__link" :href="file" target="_blank">Clique aqui</a>
                para visualizar a imagem cadastrada
            </p>

            <div class="modal__actions">
                <default-button
                    :onClick="close"
                    :outline="true"
                    text="Voltar"
                />
                <default-button
                    :onClick="save"
                    :disabled="disableButtton"
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
import FileInputVue from '../../FileInput.vue';
import axios from 'axios';

export default {
    name: 'image-modal',
    components: {
        ConfirmModal,
        DefaultButton,
        FileInputVue
    },
    data() {
        return {
            file: "",
            disableButtton: false
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
                        image_url: this.file
                    });
                }
                else {
                    successToast("Ação realizada", "Widget adicionado com sucesso");

                    this.$emit('save', {
                        image_url: this.file,
                        type: this.data.type
                    });
                }
            } catch (message) {
                errorToast("Não foi possível realizar a operação", message);
            }
        },
        async getFile(obj) {
            this.disableButtton = true;

            const formData = new FormData();
            formData.append('image', obj.file.files[0])

            const res = await axios.post(uploadImageURL, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })

            this.file = res.data.response.file;
            this.disableButtton = false;
        },
        validation() {
            if(this.file == "" || this.file == undefined)
                throw new Error("Para adicionar o widget de imagem você precisa fazer o upload de uma Imagem");
        }
    },
    props: {
        data: { type: Object, default: { image_url: "" } },
        isEdit: { type: Boolean, required: true },
        isOpen: { type: Boolean, required: true },
    },
    mounted() {
        this.file = this.data.image_url;
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

        &__input-wrapper {
            background: #252932;
            padding: 12px;
            border-bottom: 1px solid #FFFFFF;
        }

        &__text {
            font-size: 16px;
            font-weight: 600;
            line-height: 1.6;
            text-align: left;
        }

        &__link {
            color: #ADDF45;
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
