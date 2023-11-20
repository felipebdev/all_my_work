<template>
    <div>
        <Loading :is-open="isLoading" />
        <Row>
            <Col>
            <Container class="mb-4">
                <template v-slot:header-left>
                    <Title>
                        {{ section.title }}
                        <PipeVertical />
                        <span class="fw-normal">Liberação</span>
                    </Title>
                    <Subtitle>
                        Defina o tipo de liberação dos conteúdos da seção.
                    </Subtitle>
                </template>
                <template v-slot:content>
                    <Row class="custom-card">
                        <Col>
                        <Title icon="fa fa-credit-card" is-form-title>
                            Tipos de liberação
                        </Title>
                        <Subtitle class="m-0">
                            <span><b>Livre</b> - os conteúdos ficam liberados independente da conclusão dos conteúdos
                                anteriores.
                            </span>
                        </Subtitle>
                        <Subtitle class="m-0">
                            <span><b>Programada</b> - programe cada conteúdo para ser liberado em uma data específica ou em
                                X dias depois que o aluno acessou o primeiro conteúdo da seção.
                            </span>
                        </Subtitle>
                        </Col>
                    </Row>
                    <hr />

                    <DraggableTable id="deliveryDatatable" min-height>
                        <template v-slot:thead>
                            <th style="width: 60px">Nº</th>
                            <th>Nome</th>
                            <th style="width: 60px">Liberação</th>
                            <th style="width: 284px"></th>
                        </template>
                        <template v-slot:tbody>
                            <template v-if="section.section_items && section.section_items.length">
                                <draggable v-model="section.section_items" item-key="element.position" tag="tbody"
                                    ghost-class="ghost" draggable="false">
                                    <template #item="{ element }">
                                        <tr>
                                            <td class="text-center"> {{ element.position }} </td>
                                            <td class="text-center">
                                                <ContentProfile :profile="{
                                                    img: element.item_data.horizontal_image
                                                        ?? 'https://las.xgrow.com/background-default.png',
                                                    title: element.item_data.name || element.item_data.title,
                                                    subtitle: element.item_data.contentType
                                                }" />
                                            </td>
                                            <td>
                                                <SelectWithIcon v-if="element.type != 'course'"
                                                    :id="element.position + 'formDelivery'" :options="formDeliveryOptions"
                                                    v-model="element.item_data.deliveryType"
                                                    @input="updateFormDelivery($event, element)" />
                                            </td>
                                            <td>
                                                <DatePicker v-if="element.item_data.deliveryType == 'scheduled'"
                                                    v-model:value="element.item_data.dateToDisplay" :clearable="true"
                                                    :editable="false" type="date" format="DD/MM/YYYY" value-type="format"
                                                    placeholder="Selecione uma data"
                                                    @change="updateDatePicker($event, element)" />
                                            </td>
                                        </tr>
                                    </template>
                                </draggable>
                            </template>
                            <DraggableNoResult v-else :colspan="7" title="Nenhum conteúdo encontrado!"
                                subtitle="Não há dados a serem exibidos. Clique em adicionar novo para incluir." />
                        </template>
                    </DraggableTable>
                </template>
                <template v-slot:footer>
                    <hr />
                    <div class="d-flex justify-content-end">
                        <DefaultButton text="Salvar" status="success" class="w-170" @click="updateContentDelivery" />
                    </div>
                </template>
            </Container>
            </Col>
        </Row>
    </div>
</template>

<script>
import draggable from 'vuedraggable'
import DraggableTable from "../../../../js/components/XgrowDesignSystem/DraggableTable/Table.vue";
import DraggableNoResult from "../../../../js/components/XgrowDesignSystem/DraggableTable/DraggableNoResult.vue";

import Loading from "../../../../js/components/XgrowDesignSystem/Utils/Loading";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import XgrowTable from "../../../../js/components/Datatables/Table";
import NoResult from "../../../../js/components/Datatables/NoResult";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Checkbox from "../../../../js/components/XgrowDesignSystem/Form/Checkbox";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import SelectWithIcon from "../../components/SelectWithIcon.vue";
import DeliveryPeriod from "../../components/DeliveryPeriod.vue";

import { mapActions, mapStores, mapWritableState } from "pinia";
import { useSectionsStore } from '../../../../js/store/sections';
import ContentProfile from '../components/ContentProfile.vue';
import DatePicker from "vue-datepicker-next";
import '../../../../sass/plugins/datepicker_rounded.scss';
import "vue-datepicker-next/locale/pt-br";
import moment from 'moment';

export default {
    name: "ReleaseContent",
    components: {
        DeliveryPeriod,
        SwitchButton,
        Checkbox,
        Input,
        Select,
        NoResult,
        XgrowTable,
        PipeVertical,
        DefaultButton,
        Subtitle,
        Title,
        Container,
        Col,
        Row,
        Loading,
        SelectWithIcon,
        DraggableTable,
        DraggableNoResult,
        draggable,
        ContentProfile,
        DatePicker
    },
    emits: ["reloadCourse"],
    data() {
        return {
            isLoading: false,

            /** Modules */
            modules: [],
            formDeliveryOptions: [
                { value: 'sequential', name: 'Livre', img: '/xgrow-vendor/assets/img/icons/mdi-list-bulleted.svg' },
                { value: 'scheduled', name: 'Programada', img: '/xgrow-vendor/assets/img/icons/mdi-send-clock.svg' },
            ],
            teste: null
        };
    },
    computed: {
        ...mapStores(useSectionsStore),
        ...mapWritableState(useSectionsStore, ['section', 'loadingStore'])
    },
    methods: {
        //** update Content Delivery */
        async updateContentDelivery() {
            try {
                this.loadingStore.setLoading(true);


                this.section.section_items.forEach(async item => {
                    await this.updateContentDeliveryById(item);
                })

                successToast("Ação realizada", `As entregas de conteúdo foram atualizadas com sucesso!`);
            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao atualizar os itens da Seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        //** update Content Delivery By single item */
        async updateContentDeliveryById(item) {
            try {

                const query = {
                    "query": UPDATE_DELIVERY_CONTENT_MUTATION_AXIOS,
                    "variables": {
                        id: item.item_id,
                        delivered_at: item.item_data.delivered_at,
                        delivery_option: item.item_data.delivery_option
                    }
                };

                await axiosGraphqlClient.post(contentAPI, query);

            } catch (e) {
                throw new Error(e)
            }
        },
        updateFormDelivery(value, item) {
            item.item_data.delivery_option = value == 'scheduled' ? "specificDate" : null;
        },
        updateDatePicker(date, item) {
            item.item_data.delivered_at = this.formatDate(date);
            item.item_data.delivery_option = 'specificDate';
        },
        formatDate(date) {
            return moment(date, 'DD/MM/YYYY').format('YYYY-MM-DDTHH:mm:ss[Z]');
        },
        openModal(type, id = null) {
            this[type].isOpen = true;

            if (id) this[type].id = id;
        },
        closeModal(type) {
            this[type].isOpen = false;

            if (this[type].id) this[type].id = "";
            if (this[type].items) this[type].items = [];
        }
    }
};
</script>

<style lang="scss" scoped>
.custom-card {
    background: #252932;
    border-radius: 8px;
    padding: 1rem;
}

.w-50p {
    width: 50px;
}

.list-header {
    background-color: transparent;
    color: #7a7f8d;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.375rem;
    height: 36px;
}

.v-enter-from {
    opacity: 0;
}

.v-enter-active {
    transition: opacity 0.5s;
}

.v-leave-active {
    transition: opacity 0.3s;
}

.v-leave-to {
    opacity: 0;
}

.custom-accordion {
    :deep(.accordion-body) {
        background: #252831;
    }
}

.xg-container {
    position: relative;
    width: inherit;
    background-color: #252932;
    color: #FFFFFF;
    display: flex;
    align-items: center;
    padding: .3rem .6rem;
    border: 1px solid transparent;
    border-radius: 8px;
    box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 10px 0px, rgba(0, 0, 0, 0.1) 0px 0px 0px 1px;
    height: 38px;
    border: 1px solid #646D85;

    .xg-input-date {
        width: 90px;
        padding: 0 !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: #252932;
        border: none;
        color: #FFFFFF;
        text-align: center;
        font-size: .9rem;

        &::-webkit-calendar-picker-indicator {
            display: none;
        }

        &:hover,
        &:focus {
            background-color: #333844;
            cursor: pointer;
        }
    }
}
</style>
