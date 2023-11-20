<template>
    <div>
        <Loading :isOpen="isLoading" v-if="isLoading" status="saving" />

        <confirm-modal :isOpen="isOpen" modalSize="lg" class="modal">
            <template v-slot:header>
                <div class="modal__header">
                    <h1 class="modal__title">
                        {{ isEdit ? "Editar" : "Adicionar" }} conteúdo:
                        <span class="modal__title modal__title--semi-bold">
                            Doc ou PDF
                        </span>
                    </h1>
                </div>
            </template>
            <div class="modal__body">
                <p class="modal__text">Faça upload abaixo do arquivo desejado.</p>

                <div class="modal__input-wrapper">
                    <file-input-vue id="fileInput" refer="fileInput" label="Escolha um arquivo"
                        accept=".doc,.docx,.xls,.xlsx,.ppt,.pptx,.pdf,.dng" @sendFile="getFile" />
                </div>

                <p class="modal__text" v-if="file">
                    <a class="modal__link" :href="link" target="_blank">Clique aqui</a>
                    para visualizar o arquivo cadastrado ({{ name }})
                </p>

                <div class="modal__actions">
                    <default-button :onClick="close" :outline="true" text="Voltar" />
                    <default-button :onClick="save" :disabled="disableButtton" icon="fas fa-save" status="success"
                        text="Salvar" />
                </div>
            </div>
        </confirm-modal>
    </div>
</template>

<script>
import DefaultButton from '../../../Buttons/DefaultButton.vue';
import ConfirmModal from '../../../Modals/ConfirmModal.vue';
import FileInputVue from '../../FileInput.vue';
import { axiosGraphqlClient } from '../../../../../config/axiosGraphql';
import Loading from '../../../Utils/Loading.vue';

export default {
    name: 'file-modal',
    components: {
        ConfirmModal,
        DefaultButton,
        FileInputVue,
        Loading
    },
    data() {
        return {
            file: "",
            link: "",
            name: "",
            disableButtton: false,
            isLoading: false,
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
                        File: { id: this.file, name: this.name, storage_link: this.link },
                        position: this.data.position,
                        type: this.data.type,
                        file_id: this.file
                    });
                }
                else {
                    successToast("Ação realizada", "Widget adicionado com sucesso");

                    this.$emit('save', {
                        File: { id: this.file, name: this.name, storage_link: this.link },
                        file_id: this.file,
                        type: this.data.type,
                    });
                }
            } catch (message) {
                console.error(message)
                errorToast("Não foi possível realizar a ação", message)
            }
        },
        async getFile(obj) {
            this.disableButtton = true;
            this.isLoading = true;

            const formData = new FormData();
            formData.append('file', obj.file.files[0])

            await axiosGraphqlClient.post(uploadFileURL, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Access-Control-Allow-Origin': '*'
                }
            }).then(res => {
                this.file = res.data.id;
                this.name = res.data.name;
                this.link = res.data.storage_link;

                this.disableButtton = false;
            }).catch(e => {
                errorToast("Erro ao realizar upload!", "Arquivo maior do que o esperado, o tamanho máximo aceito é 6Mb")
            })

            this.isLoading = false;
        },
        validation() {
            if (file == "")
                throw new Error("Houve um erro no carregamento do arquivo.")
        }
    },
    props: {
        data: { type: Object, default: { File: { id: "", name: "", storage_link: "" } } },
        isEdit: { type: Boolean, required: true },
        isOpen: { type: Boolean, required: true },
    },
    mounted() {
        this.file = this.data.File.id;
        this.name = this.data.File.name;
        this.link = this.data.File.storage_link;
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

        &--semi-bold {
            font-weight: 600;
        }
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
