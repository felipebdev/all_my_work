<template>
    <Row>
        <Col class="my-4">
            <Title>{{isEdit ? 'Editar Live' : 'Nova live'}} <PipeVertical/> <span class="fw-500">Dados da live</span></Title>
        </Col>
        <Col>
            <SwitchButton id="isEnabled" v-model="values.isEnabled">Habilitar live</SwitchButton>
            <span class="hint">
                Caso a live já tenha ocorrido e esteja habilitada, será permitido replay da live.
            </span>
        </Col>

        <Col class="mt-4">
            <Title is-form-title>Sobre a live</Title>
        </Col>

        <Col sm="12" md="12" lg="6" xl="6">
            <Row>
                <Col>
                    <Input id="title" label="Título da live" v-model="values.title" placeholder="Título da live"/>
                </Col>
                <Col>
                    <Input id="author" label="Professor(a)" v-model="values.author" placeholder="Professor(a)"/>
                </Col>
                <Col>
                    <Input id="linkInput" type="url" label="Link da live" v-model="values.link"
                           placeholder="Link da live" has-clipboard/>
                </Col>
            </Row>
        </Col>

        <Col sm="12" md="12" lg="6" xl="6">
            <Col>
                <TextInput
                    id="description" v-model="values.description"
                    placeholder="Descrição da live"
                    label="Descrição da live"/>
            </Col>
        </Col>

        <Col class="mt-4">
            <Title is-form-title>Período</Title>
        </Col>
        <Col sm="12" md="12" lg="4" xl="4">
            <Input id="data" label="Data" type="date" v-model="values.date" placeholder="Data da live"/>
        </Col>
        <Col sm="12" md="12" lg="4" xl="4">
            <Input id="startDate" label="Horário de início" type="time" v-model="values.start"
                   placeholder="Horário de início"/>
        </Col>
        <Col sm="12" md="12" lg="4" xl="4">
            <Input id="endDate" label="Horário de fim" type="time" v-model="values.end"
                   placeholder="Horário de fim"/>
        </Col>

        <Col>
            <Select id="fuso" label="Fuso horário" v-model="values.dateTimeZone" :options="formatedTimeZone"/>
        </Col>
    </Row>
</template>

<script>
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Pagination from "../../../../js/components/Datatables/Pagination";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select";
import TextInput from "../../../../js/components/XgrowDesignSystem/Form/TextInput";

export default {
    name: "LiveData",
    components: {
        TextInput,
        SwitchButton, Col, PipeVertical, Pagination, Row, Input, DefaultButton, Subtitle, Title, Container, Select
    },
    props: {
        values: {type: Object, required: true},
        timezone: {type: Array, required: true},
        isEdit: {type: Boolean, default: false}
    },
    computed: {
        formatedTimeZone: function () {
            return this.timezone.map(item => {
                return {value: item, name: item}
            });
        }
    }
}
</script>

<style lang="scss" scoped>
.hint {
    font-size: .75rem;
    font-style: italic;
}

.fw-500 {
    font-weight: 500;
}
</style>
