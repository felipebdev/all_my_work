<template>
    <ConfirmModal :is-open="isOpen">
        <div class="modal-body__content">
            <h1>Novo conteúdo ou curso</h1>
            <p>Selecione de onde virá o conteúdo ou curso.</p>
        </div>

        <div class="d-flex flex-column content">
            <Subtitle>O conteúdo/curso será criado ou já é existente?</Subtitle>
            <div class="inputs">
                <radio-button class="mb-2" v-model="typeContent" :checked="typeContent == 'old'" option="old" label="Já existente" />
                <radio-button v-model="typeContent" :checked="typeContent == 'new'" option="new" label="Vou criar um novo" />
            </div>
        </div>

        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="$emit('close')" />
            <DefaultButton text="Salvar e prosseguir" status="success" @click="$emit('confirm', typeContent)" />
        </div>
    </ConfirmModal>
</template>

<script>
import ConfirmModal from "../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Subtitle from '../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue';
import RadioButton from '../../../../js/components/XgrowDesignSystem/Form/RadioButton.vue';

export default {
    name: 'modal-type-content',
    components: {
        ConfirmModal,
        DefaultButton,
        Subtitle,
        RadioButton
    },
    props: {
        isOpen: { type: Boolean, required: true },
    },
    data() {
        return {
            typeContent: "new"
        }
    }

}
</script>

<style lang="scss" scoped>
    .content {
        width: 100%;
        background: #333844;
        border-radius: 10px;
        padding: 20px;
    }

    .inputs {
        max-width: 200px;
        text-align: left;
    }

    .modal-body {
        &__footer {
            display: flex;
            flex-wrap: wrap;
            border-top: 1px solid #393D49;
            padding-top: 10px;
        }
    }
</style>
