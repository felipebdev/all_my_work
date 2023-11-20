<template>
    <!--colocar o modal-->
    <ConfirmModal :is-open="modal">
        <Row class="text-start w-100">
            <Col>
            <Title is-form-title icon="fas fa-columns" icon-color="#FFFFFF" icon-bg="#3f4450" class="m-0">
                Seção
            </Title>
            <hr>
            </Col>
            <Col>
            <Title is-form-title class="m-0">Configurações</Title>
            <Subtitle>
                Escolha qual seção você quer adicionar e a orientação das thumbnails.
            </Subtitle>
            </Col>
            <Col>
            <Select id="section" :options="sectionOptions" v-model="item.sectionId" label="Selecione a seção"
                placeholder="Selecione a seção" />
            </Col>
            <Col>
            <Select id="orientation" :options="orientationOptions" v-model="item.groupThumbStyle"
                label="Orientação da thumbnail" placeholder="Selecione a orientação da thumbnail" />
            </Col>
            <Col class="d-none">
            <Select id="qtdItens" :options="itensOptions" v-model.number="item.itemsCount"
                label="Selecione a quantidade de itens" placeholder="Selecione a quantidade de itens" />
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="$emit('closeModal', false)" />
            <DefaultButton text="Salvar" status="success" icon="fas fa-check"
                @click="this.$emit('closeModal', false)" />
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
import Input from "../../../../../../js/components/XgrowDesignSystem/Form/Input";
import Select from "../../../../../../js/components/XgrowDesignSystem/Form/Select";
import { mapActions, mapState, mapStores } from "pinia";
import { useDesignStartPage } from "../../../../../../js/store/design-start-page";

export default {
    name: "SectionContentModal",
    components: {
        Select, Input, Row, DefaultButton, ConfirmModal, Subtitle, Title, Col
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
                widgetName: "section",
                sectionId: null,
                itemsCount: 1,
                groupThumbStyle: "horizontal",
            },
            orientationOptions: [
                { value: "horizontal", name: "Horizontal" },
                { value: "vertical", name: "Vertical" },
            ],
            itensOptions: [
                { value: 1, name: "1 item" },
                { value: 2, name: "2 itens" },
                { value: 3, name: "3 itens" },
                { value: 4, name: "4 itens" },
                { value: 5, name: "5 itens" },
                { value: 6, name: "6 itens" },
                { value: 7, name: "7 itens" },
                { value: 8, name: "8 itens" },
                { value: 9, name: "9 itens" },
                { value: 10, name: "10 itens" },
            ]
        }
    },
    computed: {
        ...mapStores(useDesignStartPage),
        ...mapState(useDesignStartPage, ['courseOptions', 'contentOptions', 'sectionOptions'])
    },
    methods: {
        ...mapActions(useDesignStartPage, ['changeContentType', 'searchContentsByType', 'getAllProducerContent']),
    }
}
</script>

<style lang="scss" scoped>
:deep(.modal-body) {
    justify-content: space-between;
}

.img-box {
    display: flex;
    justify-content: center;
    align-items: center;
    background: #0c0e11;
    position: relative;

    &.h-image {
        width: 172px;
        height: 116px;
    }

    &.v-image {
        width: 134px;
        height: 200px;
    }

    img {
        object-fit: cover;
    }

    &__button {
        position: absolute;
        background-color: rgba(0, 0, 0, .8);
        top: 0;
        left: 0;

        .btn-move {
            position: absolute;
            border-radius: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 36px;
            height: 36px;
            padding: 0;

            &:hover {
                background-color: rgba(255, 255, 255, .5) !important;
            }
        }

        .btn-closex {
            position: absolute;
            top: -12px;
            right: -11px;
            border-radius: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 24px;
            height: 24px;
            padding: 0;

            &:hover {
                background-color: rgba(255, 255, 255, .5) !important;
            }
        }
    }
}
</style>
