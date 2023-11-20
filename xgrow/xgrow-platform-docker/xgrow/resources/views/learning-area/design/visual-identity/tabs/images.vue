<template>
    <Container>
        <template v-slot:header-left>
            <section class="section">
                <Title>Página de Login</Title>
                <p>Configure sua página nos padrões de sua empresa</p>
            </section>
        </template>
        <template v-slot:content>
            <Row class="mt-4">
                <Col sm="12" md="6" lg="6" xl="6" class="gap-4">
                    <input-switch id="useBanner" v-model="theme.useBanner">
                        Inserir imagem para página de login
                    </input-switch>
                    <p
                        class="my-2"
                        style="font-size: 12px; line-height: 16px"
                        v-if="theme.useBanner"
                    >
                        A imagem aparece na tela de login do sistema.
                    </p>

                    <div v-if="theme.useBanner">
                        <loadImage
                            v-if="theme.backgroundImageUrl"
                            :isHorizontal="true"
                            title=""
                            subtitle="Especificações: arquivo do tipo PNG, com dimensão de 1152x937px, proporção de 16:9 e tamanho máximo de 2MB."
                            :src="theme.backgroundImageUrl"
                            @remove-image="removeImage('backgroundImageUrl')"
                        />
                        <image-upload
                            v-else
                            title=""
                            subtitle="Especificações: arquivo do tipo PNG, com dimensão de 1152x937px, proporção de 16:9 e tamanho máximo de 2MB."
                            ref="background"
                            refer="background"
                            :isHorizontal="true"
                            @send-image="receiveImage"
                            :src="theme.backgroundImageUrl"
                        />
                    </div>

                    <div class="divisor"></div>

                    <Title>Identidade da plataforma</Title>

                    <section class="row">
                        <div class="col-sm-12 col-md-6 d-flex flex-column">
                            <Title :isFormTitle="true"
                                >Logo da plataforma</Title
                            >
                            <p style="font-size: 12px; line-height: 16px">
                                O logo aparece na tela de login do sistema.
                            </p>


                            <loadImage
                                v-if="theme.logoUrl"
                                :isHorizontal="true"
                                title=""
                                subtitle="Especificações: arquivo do tipo PNG, com dimensão entre 320x180px e 1280x729px, proporção de 3:1 e tamanho máximo de 2MB."
                                :src="theme.logoUrl"
                                @remove-image="removeImage('logoUrl')"
                            />
                            <image-upload
                                v-else
                                title=""
                                subtitle="Especificações: arquivo do tipo PNG, com dimensão entre 320x180px e 1280x729px, proporção de 3:1 e tamanho máximo de 2MB."
                                ref="logo"
                                refer="logo"
                                :isHorizontal="true"
                                @send-image="receiveImage"
                                :src="theme.logoUrl"
                            />

                        </div>
                        <div class="col-sm-12 col-md-6 d-flex flex-column">
                            <Title :isFormTitle="true"
                                >Favicon da plataforma</Title
                            >
                            <p style="font-size: 12px; line-height: 16px">
                                Este ícone aparece na aba so seu navegador,
                                ao lado do endereço do site.
                            </p>

                            <loadImage
                                v-if="theme.faviconUrl"
                                :isHorizontal="true"
                                title=""
                                subtitle="Especificações: arquivo do tipo PNG, com dimensão entre 320x180px e 1280x729px, proporção de 3:1 e tamanho máximo de 2MB."
                                :src="theme.faviconUrl"
                                @remove-image="removeImage('faviconUrl')"
                            />
                            <image-upload
                                v-else
                                title=""
                                subtitle="Especificações: arquivo do tipo PNG ou ICO, com dimensão de 48x48, proporção de 1:1 e tamanho máximo de 2MB."
                                ref="favicon"
                                refer="favicon"
                                @send-image="receiveImage"
                                :src="theme.faviconUrl"
                            />
                        </div>
                    </section>
                </Col>
                <Col sm="12" md="6" lg="6" xl="6">
                    <login-preview />
                </Col>
            </Row>
        </template>
    </Container>
</template>

<script>
import Col from "../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Container from "../../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Row from "../../../../../js/components/XgrowDesignSystem/Utils/Row";
import InputText from "../../../../../js/components/XgrowDesignSystem/Form/Input.vue";
import TextArea from "../../../../../js/components/XgrowDesignSystem/Form/TextInput.vue";
import InputSelect from "../../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import InputSwitch from "../../../../../js/components/XgrowDesignSystem/SwitchButton.vue";
import { useDesignVisualIdentity } from "../../../../../js/store/design-visual-identity";
import { mapWritableState, mapState, mapStores, mapActions } from "pinia";
import ImageUpload from "../../../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import LoginPreview from "../components/loginPreview.vue";
import loadImage from '../components/loadImage.vue';

export default {
    name: "images",
    components: {
        Col,
        Container,
        Title,
        Row,
        InputText,
        TextArea,
        InputSelect,
        InputSwitch,
        ImageUpload,
        LoginPreview,
        loadImage
    },
    watch: {
        'theme.useBanner': function(state) {
            if (state == false) {
                this.theme.backgroundImageUrl = null
            }
        }
    },
    computed: {
        ...mapStores(useDesignVisualIdentity),
        ...mapWritableState(useDesignVisualIdentity, ["theme", "loadingStore"]),
    },
    methods: {
        ...mapActions(useDesignVisualIdentity, ["receiveImage"]),
        removeImage(localImage) {
            this.theme[localImage] = null;
        }
    }
};
</script>

<style lang="scss" scoped>
.divisor {
    border-bottom: 1px solid #ffffff40;
    margin: 12px 0;
}
</style>
