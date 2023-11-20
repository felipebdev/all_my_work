<template>
    <Row>
        <Col class="pb-3">
        <Select id="slcRelease" :options="releaseOptions" v-model="delivery.form_delivery" class="custom-select"
            @change="selectedMode" />
        </Col>
        <transition>
            <Col class="d-flex gap-2 align-items-baseline flex-wrap" v-if="delivery.form_delivery === 'scheduled'">
            <p>A cada</p>
            <Input id="inpDays" v-model="delivery.frequency" type="number" class="custom-input" />
            <p>dia(s)</p>
            <Select id="slcModelDelivery" :options="modelOptions" v-model="delivery.delivery_model"
                class="custom-select-input" />

            <p>a partir</p>
            <Select id="slcProgramming" :options="deliveryOptions" v-model="delivery.delivery_option"
                class="custom-select-input" />

            <transition>
                <div class="d-flex gap-2 align-items-baseline pb-3" v-if="delivery.delivery_option === 'specificDate'">
                    <p>em </p>
                    <Input id="iptDateDelivery" v-model.date="delivery.delivered_at" type="date"
                        class="custom-date-input" />
                </div>
            </transition>
            </Col>
        </transition>
    </Row>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select";

export default {
    name: "DeliveryBox",
    components: { Input, Subtitle, Title, Col, Row, Select },
    props: {
        delivery: { type: Object }
    },
    data() {
        return {
            /** Select release mode */
            releaseOptions: [
                { value: 'sequential', name: 'Livre' },
                { value: 'scheduled', name: 'Programada' },
            ],
            /** Type of programming */
            deliveryOptions: [
                { value: 'startDateCourse', name: 'Ao iniciar o curso' },
                { value: 'whenSubscriberStart', name: 'Após a compra' },
                { value: 'specificDate', name: 'Data programada' },
            ],
            modelOptions: [
                { value: 'lastClass', name: 'Última aula' },
                { value: 'lastModule', name: 'Último módulo' }
            ],
        }
    },
    methods: {
        /** Reset delivery to default */
        selectedMode: function () {
            if (this.delivery.form_delivery === 'sequential') {
                this.delivery.form_delivery = 'sequential';
                this.delivery.delivery_option = 'startDateCourse';
                this.delivery.frequency = 1;
                this.delivery.started_at = new Date().toISOString().slice(0, 10);
                this.delivery.delivery_model = 'lastModule';
                this.delivery.delivered_at = null;
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.custom-select {
    margin: 0 !important;
    max-width: 200px;

    :deep(select) {
        background: #252932 !important;
        border: 1px solid #646D85 !important;
        border-radius: 8px !important;
        height: 40px !important;
        min-height: 40px !important;
    }
}

.custom-select-input {
    margin: 0 !important;

    :deep(select) {
        background: transparent !important;
        border-radius: 0 !important;
        height: 40px !important;
        min-height: 40px !important;
    }
}

.custom-input {
    margin: 0 !important;

    :deep(input) {
        background: transparent !important;
        height: 32px !important;
        width: 50px !important;
        padding: 0 10px !important;
    }
}

.custom-date-input {
    margin: 0 !important;

    :deep(input) {
        background: transparent !important;
        height: 56px !important;
        padding: 0 !important;
        margin-top: 0;
    }
}

.v-enter-from {
    opacity: 0;
}

.v-enter-active {
    transition: opacity .5s;
}

.v-leave-active {
    transition: opacity .3s;
}

.v-leave-to {
    opacity: 0;
}
</style>
