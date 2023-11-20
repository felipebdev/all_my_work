<template>
    <ConfirmModal :is-open="modal">
        <Row class="text-start w-100">
            <Col>
            <Title is-form-title icon="fas fa-clock" icon-color="#FFFFFF" icon-bg="#3f4450" class="m-0">
                Últimos Acessos
            </Title>
            <hr />
            </Col>
            <Col>
            <Title is-form-title class="m-0">Configurações</Title>
            <Subtitle>
                Escolha a quantidade itens do último acesso você quer
                mostrar.
            </Subtitle>
            </Col>
            <Col>
            <Select id="section" :options="itensOptions" v-model.number="item.lastWatchedCount"
                label="Selecione a quantidade de itens" placeholder="Selecione a quantidade de itens" />
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="$emit('closeModal', false)" />
            <DefaultButton text="Salvar" status="success" icon="fas fa-check" @click="$emit('closeModal', false)" />
        </div>
    </ConfirmModal>
</template>

<script>
import Col from "../../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Row from "../../../../../../js/components/XgrowDesignSystem/Utils/Row";
import Title from "../../../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import ConfirmModal from "../../../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import DefaultButton from "../../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Select from "../../../../../../js/components/XgrowDesignSystem/Form/Select";

export default {
    name: "LastAccessModal",
    components: {
        Select,
        Row,
        DefaultButton,
        ConfirmModal,
        Subtitle,
        Title,
        Col,
    },
    props: {
        modal: { type: Boolean, default: false },
        item: { type: Object }
    },
    data() {
        return {
            widget: {
                position: 0,
                type: "widget",
                widgetName: "lastWatched",
                lastWatchedCount: 1,
            },
            itensOptions: [
                { value: 1, name: "1 item" },
                { value: 2, name: "2 itens" },
                { value: 3, name: "3 itens" },
                { value: 4, name: "4 itens" },
                { value: 5, name: "5 itens" },
            ],
        };
    },
};
</script>

<style lang="scss" scoped>
:deep(.modal-body) {
    justify-content: space-between;
}
</style>
