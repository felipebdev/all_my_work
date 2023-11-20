<template>
    <!--colocar o modal-->
    <ConfirmModal :is-open="modal">
        <Row class="text-start w-100">
            <Col>
                <Title is-form-title icon="FONTEAWESOME ICON" icon-color="#FFFFFF" icon-bg="#3f4450" class="m-0">
                    TITULO MODAL
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
                <Select id="section" :options="sectionOptions" v-model="widget.sectionId"
                        label="Selecione a seção" placeholder="Selecione a seção"/>
            </Col>
            <Col lg="6" xl="6">
                <Select id="orientation" :options="orientationOptions" v-model="widget.orientation"
                        label="Orientação da thumbnail" placeholder="Selecione a orientação da thumbnail"/>
            </Col>
            <Col lg="6" xl="6">
                <Input id="qtdItens" label="Quantidade de itens" placeholder="Quantidade de itens"
                       v-model="widget.itemsCount" type="number" info="Coloque um valor 1 e 10." mask="##"/>
            </Col>
        </Row>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="$emit('closeModal', false)"/>
            <DefaultButton text="Salvar" status="success" icon="fas fa-check" @click="()=>{}"/>
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
import {mapActions, mapState, mapStores} from "pinia";
import {useDesignStartPage} from "../../../../../../js/store/design-start-page";

export default {
    name: "WidgetExampleModal",
    components: {
        Select, Input, Row, DefaultButton, ConfirmModal, Subtitle, Title, Col
    },
    props: {
        modal: {type: Boolean, default: false}
    },
    data() {
        return {
            widget: {
                position: 0,
                type: "widget",
                widgetName: "section",
                sectionId: null,
                itemsCount: 1,
                orientation: "horizontal",
            },
            orientationOptions: [
                {value: "horizontal", name: "Horizontal"},
                {value: "vertical", name: "Vertical"},
            ],
            sectionOptions: [
                {value: "1", name: "Seção ABC"},
                {value: "2", name: "Outra seção de teste"},
            ]
        }
    },
    computed: {
        ...mapStores(useDesignStartPage),
        ...mapState(useDesignStartPage, ['courseOptions', 'contentOptions'])
    },
    methods: {
        ...mapActions(useDesignStartPage, ['changeContentType', 'searchContentsByType', 'getAllProducerContent']),
    },
    async created() {}
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
