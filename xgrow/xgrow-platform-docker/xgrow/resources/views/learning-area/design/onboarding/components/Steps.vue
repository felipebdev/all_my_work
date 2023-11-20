<template>
    <div>
        <Row>
            <Col sm="12" md="12" lg="12" xl="6">
                <Row>
                    <Col class="mb-3">
                        <SwitchButton id="enabled" v-model="values.enabled">
                            Habilitar mapa de entrada
                        </SwitchButton>
                    </Col>
                    <Col>
                        <Title :is-form-title="true">Passos do mapa de entrada</Title>
                        <Subtitle>Adicione os passos necessários para instruir seu aluno a como utilizar a plataforma.
                        </Subtitle>
                    </Col>
                </Row>
                <Accordion id="accordionSteps">
                    <draggable :list="values.steps" item-key="order" @start="drag = true" :disabled="editMode"
                            @end="changeOrder" ghost-class="ghost">
                        <template #item="{element, index}">
                            <AccordionItem :id="`heading_${element.order}`" :title="`Passo ${index+1}`"
                                        :subtitle="element.title" :target-id="`collapse_${element.order}`"
                                        accordion-id="accordionSteps">
                                <Row>
                                    <Col>
                                        <Title is-form-title class="mt-2">Detalhes deste passo</Title>
                                    </Col>
                                    <Col>
                                        <Input id="title" label="Título" v-model="element.title" placeholder="Título"/>
                                    </Col>
                                    <Col>
                                        <Input id="subtitle" label="Subtítulo" v-model="element.subtitle"
                                            placeholder="Subtítulo"/>
                                    </Col>
                                    <Col>
                                        <Select id="contentTypeSelect" label="Tipo do conteúdo"
                                                v-model="element.contentType"
                                                :options="contentTypeOptions"/>
                                    </Col>
                                    <Col v-if="element.contentType === 'text' || element.contentType === 'textAndVideo'">
                                        <TextInput id="content" label="Conteúdo" placeholder="Conteúdo"
                                                v-model="element.content"/>
                                        <hr>
                                    </Col>
                                    <Col v-if="element.contentType === 'video' || element.contentType === 'textAndVideo'">
                                        <Input id="videoUrl" label="URL do vídeo" placeholder="URL do vídeo"
                                            v-model="element.contentUrl"/>
                                        <hr>
                                    </Col>
                                    <Col>
                                        <SwitchButton id="enableCTA" v-model="element.callToActionButton">
                                            Incluir redirecionamento (CTA)
                                        </SwitchButton>
                                        <span class="hint">Esta opção vai redirecionar seu aluno para um conteúdo específico de sua plataforma, caso ele aceite.</span>
                                    </Col>
                                    <Col v-if="element.callToActionButton">
                                        <Input id="acceptText" label="Texto de aceite" placeholder="Texto de aceite"
                                            v-model="element.callToActionLabel"/>
                                    </Col>
                                    <Col v-if="element.callToActionButton">
                                        <Input id="acceptTextUrl" label="URL do redirecionamento do aceite"
                                            placeholder="URL do redirecionamento do aceite" type="url"
                                            v-model="element.callToActionURL"/>
                                    </Col>
                                    <Col class="text-end mt-3">
                                        <DefaultButton text="" icon="fas fa-check" status="success" v-if="editMode"
                                                    @click="editMode = false" title="Sair do modo de edição"/>
                                        <DefaultButton text="" icon="fas fa-trash" status="danger" v-if="!editMode"
                                                    @click="modal.isOpen = true; orderId = element.order"/>
                                    </Col>
                                </Row>
                            </AccordionItem>
                        </template>
                    </draggable>
                </Accordion>
                <DefaultButton
                    text="Incluir um passo" icon="fas fa-plus" class="w-100 mt-3" outline @click="addStep"
                    :disabled="editMode"
                />
            </Col>

            <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                <div class="info-preview">
                    <img src="/xgrow-vendor/assets/img/lacustom/xgrowzinho-alert.svg"
                        alt="Alerta. Para visualizar o preview, necessita de no mínimo uma resolução de 600 pixels."/>
                    <div class="info-preview__content">
                        <h3>Atenção!</h3>
                        <p>Para ter uma melhor visualização do preview, é necessário possuir uma resolução de no mínimo
                            600px. Em caso de dispositivos móveis, visualizar na horizontal.</p>
                    </div>
                </div>
                <Preview :values="values.steps" :start="values.steps[0]" class="dialog-preview"/>
            </Col>
        </Row>

        <ConfirmModal :is-open="modal.isOpen">
            <i class="fa fa-question-circle fa-6x"></i>
            <div class="modal-body__content">
                <h1>Tem certeza que deseja remover este passo?</h1>
                <p>Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal-body__footer">
                <DefaultButton text="Cancelar" outline @click="modal.isOpen = false"/>
                <DefaultButton text="Excluir mesmo assim" status="success" @click="onDelete"/>
            </div>
        </ConfirmModal>
    </div>
</template>

<script>
import Row from "../../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Accordion from "../../../../../js/components/XgrowDesignSystem/Accordion/Accordion";
import AccordionItem from "../../../../../js/components/XgrowDesignSystem/Accordion/AccordionItem";
import Input from "../../../../../js/components/XgrowDesignSystem/Form/Input";
import TextInput from "../../../../../js/components/XgrowDesignSystem/Form/TextInput";
import SwitchButton from "../../../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import Select from "../../../../../js/components/XgrowDesignSystem/Form/Select";
import IconButton from "../../../../../js/components/XgrowDesignSystem/Buttons/IconButton";
import Modal from "../../../../../js/components/XgrowDesignSystem/Modals/Modal";
import draggable from 'vuedraggable'
import ConfirmModal from "../../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal";
import Preview from "./Preview";

export default {
    name: "Steps",
    components: {
        Preview,
        ConfirmModal,
        IconButton,
        SwitchButton,
        TextInput,
        Input,
        AccordionItem,
        Accordion,
        DefaultButton,
        Subtitle,
        Title,
        Col,
        Row,
        Select,
        draggable,
        Modal
    },
    props: {
        values: {type: Object, required: true}
    },
    data() {
        return {
            drag: false,
            enabled: true,
            modal: {
                isOpen: false
            },
            contentTypeOptions: [
                {value: 'text', name: 'Texto'},
                {value: 'video', name: 'Vídeo'},
                {value: 'textAndVideo', name: 'Texto e vídeo'},
            ],
            orderId: 0,
            editMode: false,
        }
    },
    methods: {
        addStep: function () {
            this.editMode = true;
            if (this.$props.values.steps.length > 0) {
                this.$props.values.steps.push({
                    title: "",
                    subtitle: "",
                    order: this.$props.values.steps.length + 1,
                    contentType: "text",
                    content: "",
                    contentUrl: "",
                    callToActionLabel: "",
                    callToActionURL: "",
                    callToActionButton: false
                });
            } else {
                this.$props.values.steps.push({
                    title: "",
                    subtitle: "",
                    order: 0,
                    contentType: "text",
                    content: "",
                    contentUrl: "",
                    callToActionLabel: "",
                    callToActionURL: "",
                    callToActionButton: false
                });
            }
        },
        onDelete: function () {
            this.$props.values.steps = this.$props.values.steps.filter(item => item.order !== this.orderId);
            this.modal.isOpen = false;
            successToast("Passo removido!", "Clique em salvar para persistir as alterações.");
        },
        changeOrder: function () {
            this.drag = false
            let index = 1;
            this.$props.values.steps = this.$props.values.steps.map(item => {
                item.order = index;
                index++;
                return item;
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.hint {
    font-size: .75rem;
    font-style: italic;
    padding-left: 8px;
}

.ghost {
    cursor: grab !important;
    background: #222329 !important;
    border: 2px dashed #93BC1E !important;
    border-radius: 6px !important;
    padding: 10px 10px 25px 10px;
}

.info-preview {
    display: none;
    width: 100%;
    padding: 10px;
    font-family: 'Open Sans', serif;
    background: #3D3736;
    box-shadow: 0 4px 10px rgba(18, 25, 43, 0.05);
    border-radius: 6px;
    color: #F0BB7D;
    border-left: 6px solid #E28A22;
    gap: 1rem;

    &__content {
        h3 {
            font-weight: 700;
            font-size: 1rem;
            line-height: 1.6rem;
        }

        p {
            font-weight: 400;
            font-size: 0.875rem;
            line-height: 1.375rem;
        }
    }
}

.dialog-preview {
    display: block;
}

@media only screen and (max-width: 600px) {
    .info-preview {
        display: flex;
    }

    .dialog-preview {
        display: none;
    }
}
</style>
