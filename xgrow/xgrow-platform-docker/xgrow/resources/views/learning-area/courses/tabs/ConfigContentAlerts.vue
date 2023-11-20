<template>
    <Row>
        <Col>
            <Title>Configurações <PipeVertical/> <span class="fw-normal">Avisos</span></Title>
        </Col>

        <Col class="mt-4">
            <Row>
                <Col>
                    <Title is-form-title class="mb-0">Avisos</Title>
                    <Subtitle class="mb-3">Ative os avisos que você deseja que seu alunos recebam.</Subtitle>
                </Col>
                <Col class="mb-3">
                    <SwitchButton id="alertAddContent" :value="alerts.addContent">
                        Notificar os alunos sempre que algum conteúdo for adicionado
                    </SwitchButton>
                </Col>
                <Col class="mb-3">
                    <SwitchButton id="alertAddModule" :value="alerts.addModule">
                        Notificar os alunos sempre que algum módulo for adicionado
                    </SwitchButton>
                </Col>
                <Col class="mb-3">
                    <SwitchButton id="alertContentRelease" :value="alerts.contentRelease">
                        Notificar os alunos sempre que algum conteúdo for liberado (caso tenha conteúdos programados)
                    </SwitchButton>
                </Col>
                <Col class="mb-3">
                    <SwitchButton id="alertStartLive" :value="alerts.startLive">
                        Notificar os alunos sempre que iniciar uma transmissão ao vivo
                    </SwitchButton>
                </Col>
                <Col class="mb-3">
                    <SwitchButton id="alertChangeContent" :value="alerts.changeContent">
                        Notificar os alunos sempre que alguma alteração for feita em algum conteúdo ou módulo
                    </SwitchButton>
                </Col>
            </Row>
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
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";

export default {
    name: "ConfigContentAlerts",
    components: {
        SwitchButton,
        PipeVertical, Subtitle, Title, Container, Col, Row
    },
    data() {
        return {
            alerts: {
                addContent: false,
                addModule: false,
                contentRelease: false,
                startLive: false,
                changeContent: false,
            }
        }
    },
    methods: {},
}
</script>

<style scoped>
</style>
