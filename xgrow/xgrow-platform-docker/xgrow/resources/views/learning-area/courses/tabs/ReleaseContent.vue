<template>
    <LoadingStore />
    <Row>
        <Col>
        <Container class="mb-4">
            <template v-slot:header-left>
                <Title>{{ course.name }}
                    <PipeVertical /> <span class="fw-normal">Liberação</span>
                </Title>
                <Subtitle>Defina o tipo de liberação dos módulos e aulas do seu curso.</Subtitle>
            </template>
            <template v-slot:content>
                <Row class="custom-card">
                    <Col>
                    <Title icon="fa fa-cc" is-form-title>Tipos de liberação</Title>
                    <Subtitle class="m-0">
                        <span><b>Livre</b> - os conteúdos ficam liberados independente da conclusão dos conteúdos
                            anteriores.</span>
                    </Subtitle>
                    <Subtitle class="m-0">
                        <span><b>Programada</b> - programe cada conteúdo para ser liberado em uma data específica ou em
                            X dias depois que o aluno iniciou o curso.</span>
                    </Subtitle>
                    </Col>
                </Row>
                <hr>
                <Row class="mb-2">
                    <Col>
                    <SwitchButton id="chkAutomatic" v-model="delivery.enable">
                        Aplicar a liberação programada nos módulos subsequentes
                    </SwitchButton>
                    </Col>
                </Row>
                <transition>
                    <Row class="custom-card mb-4" v-if="delivery.enable">
                        <Col sm="12" md="12" lg="12" xl="12" class="pt-2">
                        <Title is-form-title>Forma de entrega:</Title>
                        </Col>
                        <DeliveryPeriod :id="`deliveryPeriod}`" v-model:formDelivery="delivery.form_delivery"
                            v-model:frequency="delivery.frequency" v-model:deliveryModel="delivery.delivery_model"
                            v-model:deliveryOption="delivery.delivery_option" v-model:deliveredAt="delivery.delivered_at" />
                        <Col>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <DefaultButton text="Aplicar" status="success" class="w-170" @click="changeDelivery" />
                        </div>
                        </Col>
                    </Row>
                </transition>
                <div class="list-header d-flex" v-if="modules.length > 0">
                    <p style="margin-right: 25px;"></p>
                    <p class="text-center" style="margin-right: 15px;">Nº</p>
                    <p class="w-100">Nome</p>
                    <p style="margin-right: 12px;"></p>
                    <!-- Gerar calc js margin-right: calc(486px - 44px); -->
                    <p style="margin-right: 160px;">Liberação</p>
                </div>
                <Accordion id="releaseAccordion">
                    <template v-if="modules.length > 0">
                        <template v-for="(element, index) in modules" key="element.id">
                            <AccordionItem :id="`heading_rel_${element.id}`" :target-id="`collapse_rel_${element.id}`"
                                class="custom-accordion" accordion-id="releaseAccordion" has-html-header
                                :is-open="index === 0">
                                <template v-slot:header>
                                    <!-- Module Header -->
                                    <ReleaseModuleRow :item="element" :index="index" />
                                </template>
                                <template v-slot:default>
                                    <!-- Content -->
                                    <template v-for="(content, ci) in element.Content" key="content.id">
                                        <ReleaseContentRow :item="content" :index="ci" />
                                    </template>
                                </template>
                            </AccordionItem>
                        </template>
                    </template>
                    <table class="w-100" v-else>
                        <tbody>
                            <NoResult :colspan="1" title="Nenhum módulo encontrado!"
                                subtitle="Não há dados a serem exibidos. Clique em adicionar para adicionar um módulo." />
                        </tbody>
                    </table>
                </Accordion>
            </template>
            <template v-slot:footer>
                <hr>
                <div class="d-flex justify-content-between">
                    <DefaultButton text="Cancelar" outline class="w-170" />
                    <DefaultButton text="Salvar" status="success" class="w-170" @click="saveDelivery" />
                </div>
            </template>
        </Container>
        </Col>
    </Row>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical.vue";
import XgrowTable from "../../../../js/components/Datatables/Table.vue";
import NoResult from "../../../../js/components/Datatables/NoResult.vue";
import ReleaseContentRow from "./ReleaseContentRow.vue";
import { GET_MODULES_DELIVERY_QUERY_AXIOS } from "../../../../js/graphql/queries/modules";
import AccordionItem from "../../../../js/components/XgrowDesignSystem/Accordion/AccordionItem.vue";
import Accordion from "../../../../js/components/XgrowDesignSystem/Accordion/Accordion.vue";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Checkbox from "../../../../js/components/XgrowDesignSystem/Form/Checkbox.vue";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton.vue";
import ReleaseModuleRow from "./ReleaseModuleRow.vue";
import SelectWithIcon from "../../components/SelectWithIcon.vue";
import DeliveryPeriod from "../../components/DeliveryPeriod.vue";
import { useLoadingStore } from "../../../../js/store/components/loading";
import { mapActions, mapStores } from "pinia";
import LoadingStore from "../../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import { axiosGraphqlClient } from "../../../../js/config/axiosGraphql";
import { UPDATE_DELIVERY_CONTENT_MUTATION_AXIOS } from "../../../../js/graphql/mutations/contents";

export default {
    name: "ReleaseContent",
    components: {
        LoadingStore,
        DeliveryPeriod,
        ReleaseModuleRow,
        SwitchButton,
        Checkbox,
        Input,
        Accordion,
        AccordionItem,
        ReleaseContentRow, Select,
        NoResult, XgrowTable, PipeVertical, DefaultButton, Subtitle, Title, Container, Col, Row, SelectWithIcon
    },
    props: { course: { type: Object } },
    data() {
        return {
            /** Modules */
            modules: [],

            delivery: {
                enable: false,
                form_delivery: 'sequential', //releaseOptions
                delivery_option: 'specificDate', //deliveryOptions
                frequency: 1,
                delivered_at: new Date().toISOString().slice(0, 10),
                delivery_model: 'lastModule', //modelOptions
                started_at: new Date().toISOString().slice(0, 10) // Only specificDate
            },
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        /** Get Modules */
        getModules: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_MODULES_DELIVERY_QUERY_AXIOS,
                    "variables": { course_id: this.$route.params.id, page: 1, limit: 50 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { modules } = res.data.data;
                this.modules = modules.data.map((item) => {
                    item.Content.map((content) => {
                        if (content.delivered_at)
                            content.delivered_at = content.delivered_at.split("T")[0]
                        return content
                    })
                    return item
                });
                this.loadingStore.setLoading();
            } catch (e) {
                console.log(e);
            }
        },
        /** Change delivery by batch */
        changeDelivery: function () {
            const delivery = this.delivery;
            let count = 0;
            this.modules.forEach(module => {
                // if (delivery.delivery_model === 'lastModule') count = 0
                if (module.Content.length > 0) {
                    module.Content.forEach(content => {
                        if (delivery.form_delivery === 'sequential') {
                            content.delivered_at = delivery.delivered_at;
                            content.delivery_model = delivery.delivery_model;
                            content.frequency = parseInt(delivery.frequency);
                            content.started_at = delivery.started_at + "T00:00:00.000Z";
                            content.delivery_option = delivery.delivery_option;
                            content.form_delivery = delivery.form_delivery;
                        } else {
                            // count += parseInt(delivery.frequency);
                            content.delivered_at = delivery.delivered_at;
                            content.delivery_model = delivery.delivery_model;
                            content.frequency = parseInt(delivery.frequency);
                            content.started_at = delivery.started_at + "T00:00:00.000Z";
                            content.delivery_option = delivery.delivery_option;
                            content.form_delivery = delivery.form_delivery;
                        }
                    });
                }
            });
            successToast("Liberação programada!", `Para que as alterações tenham efeitos, clique em salvar!`);
        },
        saveDelivery: async function () {
            this.loadingStore.setLoading(true);
            for (const module of this.modules) {
                if (module.Content.length > 0) {
                    for (const content of module.Content) {

                        const query = {
                            "query": UPDATE_DELIVERY_CONTENT_MUTATION_AXIOS,
                            "variables": {
                                id: content.id,
                                delivered_at: content.delivered_at,
                                delivery_model: content.delivery_model,
                                frequency: parseInt(content.frequency),
                                started_at: content.started_at,
                                delivery_option: content.delivery_option,
                                form_delivery: content.form_delivery
                            }
                        };
                        await axiosGraphqlClient.post(contentAPI, query);
                    }
                }
            }
            this.loadingStore.setLoading();
            successToast("Liberação programada!", `A liberação foi salva com sucesso!`);
        }
    },
    async created() {
        await this.getModules();
    }
}
</script>

<style lang="scss" scoped>
.custom-card {
    background: #252932;
    border-radius: 8px;
    padding: 1rem;
}

.list-header {
    background-color: transparent;
    color: #7A7F8D;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.375rem;
    height: 36px;
}

.custom-accordion {
    :deep(.accordion-body) {
        background: #252831;
    }
}
</style>
