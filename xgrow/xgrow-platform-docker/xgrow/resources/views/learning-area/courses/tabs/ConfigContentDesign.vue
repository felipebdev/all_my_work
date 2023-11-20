<template>
    <Row>
        <Col>
            <Title>Configurações <PipeVertical/> <span class="fw-normal">Design</span></Title>
        </Col>

        <Col sm="12" md="12" lg="6" xl="6" class="mt-4">
            <Row>
                <Col>
                    <Title is-form-title class="mb-0">Visualização dos módulos</Title>
                    <Subtitle>Configure a forma de visualização dos módulos do curso.</Subtitle>
                </Col>
                <Col>
                    <Multiselect
                        :options="designOptions"
                        v-model="selectedDesign"
                        placeholder="Visualização dos módulos"
                        :canClear="false"
                        @select="()=>{}"
                    >
                    </Multiselect>
                </Col>
            </Row>
        </Col>

        <Col sm="12" md="12" lg="6" xl="6">
            <DesignModulePreview :view="selectedDesign"/>
        </Col>
    </Row>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import DesignModulePreview from "../components/DesignModulePreview";

export default {
    name: "ConfigContentDesign",
    components: {
        DesignModulePreview,
        Multiselect, PipeVertical, Subtitle, Title, Container, Col, Row
    },
    data() {
        return {
            designOptions: [
                {value: 'list', label: "Lista (padrão)"},
                {value: 'thumbnails', label: "Thumbnail"},
            ],
            selectedDesign: 'list'
        }
    },
    methods: {},
}
</script>

<style scoped>
</style>
