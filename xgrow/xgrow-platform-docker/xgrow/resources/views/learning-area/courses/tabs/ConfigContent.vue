<template>
    <Row>
        <Col>
        <Container class="mb-4">
            <template v-slot:content>
                <XgrowTab id="tabCourse">
                    <template v-slot:header>
                        <XgrowTabNav :items="tabs.items" id="tabConfigNav" start-tab="tabConfigAbout"
                            @change-page="(val) => { tabs.active = val }" />
                    </template>
                    <template v-slot:body>
                        <div class="mb-4"></div>
                        <XgrowTabContent id="tabConfigAbout" :selected="tabs.active === 'tabConfigAbout'">
                            <ConfigContentAbout />
                        </XgrowTabContent>
                        <XgrowTabContent id="tabConfigDesign" :selected="tabs.active === 'tabConfigDesign'">
                            <ConfigContentDesign />
                        </XgrowTabContent>
                        <XgrowTabContent id="tabConfigAlert" :selected="tabs.active === 'tabConfigAlert'">
                            <ConfigContentAlerts />
                        </XgrowTabContent>
                        <XgrowTabContent id="tabConfigCertificate" :selected="tabs.active === 'tabConfigCertificate'">
                            <ConfigContentCertificate />
                        </XgrowTabContent>
                    </template>
                </XgrowTab>
            </template>
        </Container>
        </Col>
    </Row>
</template>

<script>
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import XgrowTab from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTab.vue";
import XgrowTabNav from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import XgrowTabContent from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import ConfigContentAbout from "./ConfigContentAbout.vue";
import ConfigContentDesign from "./ConfigContentDesign.vue";
import ConfigContentAlerts from "./ConfigContentAlerts.vue";
import ConfigContentCertificate from "./ConfigContentCertificate.vue";

export default {
    name: "ConfigContent",
    components: {
        ConfigContentCertificate,
        ConfigContentAlerts,
        ConfigContentDesign,
        ConfigContentAbout,
        XgrowTabContent,
        XgrowTabNav,
        XgrowTab,
        DefaultButton,
        Container, Col, Row
    },
    emits: ['reloadCourse'],
    data() {
        return {
            /** Tab */
            tabs: {
                active: 'tabConfigAbout',
                items: [
                    { title: "Sobre o curso", screen: "tabConfigAbout" },
                    { title: "Design", screen: "tabConfigDesign" },
                    // {title: "Avisos (Beta)", screen: "tabConfigAlert"},
                    // {title: "Provas (Beta)", screen: "tabConfigExam"},
                    // {title: "Certificado (Beta)", screen: "tabConfigCertificate"},
                ],
            },

        }
    },
}
</script>
