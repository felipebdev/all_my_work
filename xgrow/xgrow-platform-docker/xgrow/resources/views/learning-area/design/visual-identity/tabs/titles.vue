<template>
    <Container>
        <template v-slot:header-left>
            <section class="section">
                <Title>Títulos e Rodapé</Title>
                <p>Insira informações complementares sobre sua plataforma</p>
            </section>
        </template>
        <template v-slot:content>
            <div class="d-flex flex-column w-100 gap-4">
                <Row class="w-100">
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <input-text id="platform-title" label="Título da plataforma" v-model="theme.platformName" />
                    <input-text id="footer-text" label="Texto do rodapé" v-model="theme.footer" />
                    </Col>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <text-area id="description" label="Descrição" style="max-height: 142px" v-model="theme.description" />
                    </Col>
                </Row>

                <Row class="w-100">
                    <Title>Informações para suporte</Title>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <input-text id="email" label="Endereço de email" v-model="theme.supportEmail" />
                    </Col>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <input-text id="phone" label="Número de telefone" v-model="theme.supportPhone" />
                    </Col>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <input-text id="url" label="URL" v-model="theme.supportLink" />
                    </Col>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <Select id="supportType" label="Contato padrão" :options="supportOptions" v-model="theme.supportType" />
                    </Col>
                </Row>

                <Row class="w-100">
                    <Title>Palavras-chave</Title>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <Input-key-word id="platform-keywords" label="Adicionar" v-model="keyword" :add="true" :remove="false"
                        @add="addKeyword" />
                    </Col>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0" v-for="(keyword, i) in keywords" :key="i">
                    <Input-key-word id="platform-keywords" :label="'Palavra-chave ' + (i + 1)" v-model="keywords[i]"
                        :add="false" :remove="true" @remove="removeKeyword(i)" />
                    </Col>
                </Row>
            </div>
        </template>
    </Container>
</template>

<script>
import Col from "../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title";
import Row from "../../../../../js/components/XgrowDesignSystem/Utils/Row";
import InputText from "../../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import TextArea from "../../../../../js/components/XgrowDesignSystem/Form/TextInput.vue";
import InputSelect from "../../../../../js/components/XgrowDesignSystem/Form/Select.vue";

import { useDesignVisualIdentity } from "../../../../../js/store/design-visual-identity";
import { mapWritableState, mapState, mapStores } from "pinia";
import InputKeyWord from '../components/InputKeyWord.vue';
import Select from "../../../../../js/components/XgrowDesignSystem/Form/Select.vue";

export default {
    name: "titles",
    components: {
        Col,
        Container,
        Title,
        Row,
        InputText,
        TextArea,
        InputSelect,
        InputKeyWord,
        Select
    },
    data() {
        return {
            supportOptions: [
                { value: "email", name: "Por email" },
                { value: "link", name: "Via link" },
                { value: "phone", name: "Por telefone" },
            ],
            keyword: ""
        };
    },
    computed: {
        ...mapStores(useDesignVisualIdentity),
        ...mapWritableState(useDesignVisualIdentity, ["theme", "keywords"]),
    },
    methods: {
        addKeyword() {
            this.keywords.push(this.keyword);
            this.theme.keywords = this.keywords.join(';');
        },
        removeKeyword(index) {
            this.keywords.splice(index, 1);
            this.theme.keywords = this.keywords.join(';');
        }
    }
};
</script>

<style lang="scss" scoped></style>
