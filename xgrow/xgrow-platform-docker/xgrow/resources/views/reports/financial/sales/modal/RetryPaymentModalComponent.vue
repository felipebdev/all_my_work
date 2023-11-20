<template>
    <modal-component :isOpen="isOpen" @close="closeFunction">
        <template v-slot:title>Realizar cobrança novamente</template>
        <template v-slot:content>
            <div class="content" v-if="!modalData.error">
                <i class="fa fa-check-circle fa-7x my-3" style="color: #93BC1E"></i>
                <Title>Cobrança realizada com sucesso!</Title>
                <Subtitle style="max-width: 420px">
                    A nova cobrança foi realizada com sucesso e o status do pagamento logo será atualizado para <span
                    style="color: #93BC1E">Pago</span>.
                </Subtitle>
            </div>

            <div class="content" v-if="modalData.error">
                <i class="fa fa-times-circle fa-7x my-3" style="color: #EB5757"></i>
                <Title>Falha ao tentar realizar a cobrança novamente!</Title>
                <Subtitle style="max-width: 420px">
                    {{ modalData.message }}
                </Subtitle>
            </div>
        </template>

        <template v-slot:footer>
            <div class="content my-2">
                <DefaultButton text="OK" status="success" @click.prevent="closeFunction" style="width: 130px"/>
            </div>
        </template>
    </modal-component>
</template>

<script>
import ModalComponent from "../../../../../js/components/ModalComponent.vue";
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

export default {
    name: "retry-payment-modal",
    components: {
        DefaultButton,
        Subtitle,
        Title,
        "modal-component": ModalComponent,
    },
    props: {
        isOpen: {
            type: Boolean,
            required: true,
        },
        closeFunction: {
            type: Function,
            required: true,
        },
        modalData: {
            type: Object,
            required: true,
        },
    }
};
</script>

<style scoped lang="scss">
:deep(.modal-body) {
    padding: 0;
    width: 100%;
    flex-direction: column;
    justify-content: flex-start;
}

:deep(.modal-footer) {
    padding: 0;
}

.content {
    text-align: center;
    min-width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}
</style>
