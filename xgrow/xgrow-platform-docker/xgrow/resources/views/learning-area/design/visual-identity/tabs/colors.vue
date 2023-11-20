<template>
    <Container>
        <template v-slot:content>
            <Row>
                <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                    <section
                        class="section colors"
                        v-if="Object.keys(theme).length"
                    >
                        <Title>Cores</Title>
                        <p>
                            Clique na cor primária para experimentar outras combinações
                        </p>

                        <input-color
                            class="mt-4"
                            subtitle="Essa será a cor principal dos botões e ícones"
                            title="Primária"
                            type="primary"
                            v-model="theme.primaryColor"
                        />
                        <input-color
                            subtitle="Cor do seu menu lateral e ordenação de conteúdo"
                            title="Secundária"
                            type="secondary"
                            v-model="theme.secondaryColor"
                        />
                        <input-color
                            title="Terciária"
                            v-model="theme.tertiaryColor"
                        />
                        <input-color title="Textos" v-model="theme.textColor" />
                        <input-color
                            title="Campos"
                            v-model="theme.inputColor"
                        />
                        <div class="divisor"></div>
                    </section>

                    <section
                        class="section background"
                        v-if="Object.keys(theme).length"
                    >
                        <Title :isFormTitle="true">Background</Title>

                        <div class="background-radio">
                            <RadioButton
                                :checked="theme.backgroundType == 'solid'"
                                id="solid"
                                label="Cor sólida"
                                name="background-type"
                                option="solid"
                                v-model="theme.backgroundType"
                            />
                            <RadioButton
                                :checked="theme.backgroundType == 'gradient'"
                                id="gradient"
                                label="Cor degradê"
                                name="background-type"
                                option="gradient"
                                v-model="theme.backgroundType"
                            />
                        </div>

                        <div
                            class="background-types"
                            v-if="theme.backgroundType"
                        >
                            <input-color
                                title="Cor de fundo"
                                v-if="theme.backgroundType == 'solid'"
                                v-model="theme.backgroundColor"
                            />

                            <input-color
                                title="Cor 1"
                                v-if="theme.backgroundType == 'gradient'"
                                v-model="theme.backgroundGradientSecond"
                            />

                            <input-color
                                title="Cor 2"
                                v-if="theme.backgroundType == 'gradient'"
                                v-model="theme.backgroundGradientFirst"
                            />
                        </div>

                        <input-range
                            :key="'slider' + 0"
                            :min="0"
                            :max="360"
                            v-if="
                                Object.keys(theme).length &&
                                theme.backgroundType == 'gradient'
                            "
                            v-model.number="theme.backgroundGradientDegree"
                        >
                            <template v-slot:values="{ value, min, max }">
                                <div class="input-range__values">
                                    <div class="input-range__values__text">
                                        {{ min }}º
                                    </div>
                                    <div
                                        class="input-range__values__text input-range__values__text--current"
                                    >
                                        {{ value }}º
                                    </div>
                                    <div class="input-range__values__text">
                                        {{ max }}º
                                    </div>
                                </div>
                            </template>
                        </input-range>
                        <div class="divisor"></div>
                    </section>

                    <section
                        class="section border-radius"
                        v-if="Object.keys(theme).length"
                    >
                        <Title :isFormTitle="true">
                            Arredondamento das bordas
                        </Title>

                        <p>
                            Defina o padrão de arredondamento dos campos e
                            botões da sua plataforma
                        </p>

                        <border-radius-example class="my-4" />

                        <input-range
                            :key="'slider' + 1"
                            :max="100"
                            :min="0"
                            v-if="Object.keys(theme).length"
                            v-model.number="theme.borderRadius"
                        >
                            <template v-slot:values="{ value, min, max }">
                                <div class="input-range__values">
                                    <div class="input-range__values__text">
                                        {{ min }}%
                                    </div>
                                    <div
                                        class="input-range__values__text input-range__values__text--current"
                                    >
                                        {{ value }}%
                                    </div>
                                    <div class="input-range__values__text">
                                        {{ max }}%
                                    </div>
                                </div>
                            </template>
                        </input-range>

                        <p class="highlight my-4">
                            *Seus padrões serão aplicados em toda a plataforma
                        </p>
                    </section>
                </Col>
                <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">

                    <div class="d-flex justify-content-end gap-5">
                        <InputSelect
                            id="change-screen"
                            label="Visualização"
                            v-model="pageView"
                            :options="pageOptions"
                        />

                        <PreviewPlatform :device="device" @device="(payload) => device = payload" />
                    </div>

                    <main-page-preview v-if="pageView == 'mainPage'" :device="device" />
                    <login-preview v-if="pageView == 'login'" :device="device" />
                    <course-preview v-if="pageView == 'coursePage'" :device="device" />
                </Col>
            </Row>
        </template>
    </Container>
</template>

<script>
import Col from "../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../../../js/components/XgrowDesignSystem/Cards/Container";
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Row from "../../../../../js/components/XgrowDesignSystem/Utils/Row";
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title";
import inputColor from "../components/inputColor";
import inputRange from "../components/inputRange";
import borderRadiusExample from "../components/borderRadiusExample";
import { useDesignVisualIdentity } from "../../../../../js/store/design-visual-identity";
import { mapWritableState, mapStores } from "pinia";
import RadioButton from "../../../../../js/components/XgrowDesignSystem/Form/RadioButton";
import InputSelect from "../../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import loginPreview from '../components/loginPreview.vue';
import mainPagePreview from '../components/mainPagePreview.vue';
import coursePreview from '../components/coursePreview.vue';
import PreviewPlatform from '../../../../../js/components/XgrowDesignSystem/Utils/PreviewPlatform.vue';

export default {
    name: "colors",
    components: {
        Col,
        Container,
        DefaultButton,
        Row,
        Title,
        inputColor,
        RadioButton,
        inputRange,
        borderRadiusExample,
        InputSelect,
        loginPreview,
        mainPagePreview,
        coursePreview,
        PreviewPlatform
    },
    data() {
        return {
            pageView: "mainPage",
            pageOptions: [
                { value: "mainPage", name: "Página Principal" },
                { value: "login", name: "Login" },
                { value: "coursePage", name: "Página de Curso" },
            ],
            device: 'desktop'
        };
    },
    computed: {
        ...mapStores(useDesignVisualIdentity),
        ...mapWritableState(useDesignVisualIdentity, ["theme"]),
    },
};
</script>

<style lang="scss" scoped>
.section {
    max-width: 400px;
    font: normal normal 400 12.8px/17.43px "Open Sans", sans-serif;
}

.divisor {
    border-bottom: 1px solid #ffffff40;
    max-width: 400px;
    margin: 24px 0;
}

.colors {
    display: flex;
    flex-direction: column;
}

.background {
    &-radio,
    &-types {
        display: flex;
        gap: 20px;
    }
}

.highlight {
    font-style: italic;
}

.input-range {
    &__values {
        display: flex;
        justify-content: space-between;

        &__text {
            font-size: 12.8px;
            line-height: 17.43px;

            &--current {
                font-size: 16px;
                font-weight: 700;
                line-height: 21, 79px;
            }
        }
    }
}
</style>
