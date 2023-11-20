<template>
    <ConfirmModal :is-open="isOpen">
        <Row class="w-100">
            <Title class="justify-content-center">
                {{ isEdit ? 'Editar' : 'Criar' }} notificação
            </Title>
            <Subtitle class="justify-content-center">
                Insira os dados da notificação:
            </Subtitle>
        </Row>
        <div class="modal-body__content">
            <Row>
                <Col class="mb-2">
                    <Input id="notificationTitle" label="Título da notificação" v-model="form.title" placeholder="Título da notificação..." />
                </Col>
                <Col class="mb-2">
                    <TextInput id="notificationText" label="Texto da mensagem" v-model="form.text" placeholder="Digite seu texto aqui..." />
                </Col>
                <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
                    <DatePicker
                        class="w-100"
                        v-model:value="form.date"
                        format="DD/MM/YYYY"
                        :clearable="true"
                        type="date"
                        placeholder="Data do evento"
                        value-type="format"
                    />
                </Col>
                <Col sm="12" md="6" lg="6" xl="6">
                    <DatePicker
                        class="w-100"
                        v-model:value="form.time"
                        format="HH:mm"
                        :clearable="true"
                        type="time"
                        placeholder="Hora do envio"
                        value-type="format"
                    />
                </Col>
            </Row>
        </div>
        <HeaderLine />
        <div class="modal-body__footer mt-0">
            <hr>
            <DefaultButton text="Cancelar" outline @click="$emit('close')" />
            <DefaultButton text="Salvar" :disabled="false" status="success" @click="sendPayload" />
        </div>
    </ConfirmModal>
</template>

<script>
import DefaultButton from '../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue'
import Input from '../../../../js/components/XgrowDesignSystem/Form/Input.vue'
import TextInput from '../../../../js/components/XgrowDesignSystem/Form/TextInput.vue'
import ConfirmModal from '../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue'
import Subtitle from '../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue'
import Title from '../../../../js/components/XgrowDesignSystem/Typography/Title.vue'
import Col from '../../../../js/components/XgrowDesignSystem/Utils/Col.vue'
import Row from '../../../../js/components/XgrowDesignSystem/Utils/Row.vue'
import HeaderLine from '../../../../js/components/XgrowDesignSystem/Utils/HeaderLine.vue'

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

export default {
    name: 'Create-or-Edit-Notification',
    components: {
        Col,
        ConfirmModal,
        DatePicker,
        DefaultButton,
        HeaderLine,
        Input,
        Row,
        Subtitle,
        TextInput,
        Title,
    },
    props: {
        form: { type: Object, required: true },
        isEdit: { type: Boolean, default: false },
        isOpen: { type: Boolean, required: true },
    },
    methods: {
        sendPayload() {
            this.$emit('payload', this.form)
        },
    },
    mounted() {
        document.querySelector('.modal-sections.modal').style.overflow = 'hidden';
        document.querySelector('body').style.overflow = 'hidden';
    },
    unmounted() {
        document.querySelector('.modal-sections.modal').style.overflow = 'auto';
        document.querySelector('body').style.overflow = 'auto';
    }
}
</script>

<style lang="scss">
    .text-area-label {
        text-align: initial;
    }

    .mx-datepicker-popup {
        z-index: 99999999999999999;
    }

    .mx-time {
        -webkit-box-flex: 1!important;
        -ms-flex: 1!important;
        flex: 1!important;
        width: 224px!important;
        background: #252932!important;
    }

    .mx-time-column .mx-time-item.active {
        color: #ADFF2F!important;
    }

    .mx-time-option.active {
        color: #ADFF2F!important;
    }

    .mx-time-column .mx-time-list::after {
        height: 0!important;
    }

    .modal-body__footer {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    @media (max-width: 576px) {
        .modal-body__footer {
            .btn {
                width: 100%;
            }
        }
    }
</style>