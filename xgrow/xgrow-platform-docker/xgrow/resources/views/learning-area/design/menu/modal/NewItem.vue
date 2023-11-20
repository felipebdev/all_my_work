<template>
    <Modal @close="close">
        <section class="new-item">
            <header class="new-item__header">
                <Title class="title">
                    <i class="fas fa-plus-circle"></i>
                    Novo item do menu
                </Title>
            </header>

            <div class="new-item__body">
                <Row>
                    <Subtitle class="subtitle">
                        Especifique o conteúdo de seu novo item.
                    </Subtitle>
                </Row>

                <Row class="justify-content-between">
                    <Col sm="4" md="4" lg="4" xl="4">
                        <p class="label">Ícone</p>

                        <div class="icon__wrapper">
                            <div class="thumb">
                                <div
                                    class="icon"
                                    :key="selectedIcon.icon.name"
                                    v-if="selectedIcon.icon"
                                >
                                    <svg
                                        style="fill: #93bc1f"
                                        :title="selectedIcon.icon.name"
                                        :view-box.camel="
                                            selectedIcon.icon.viewBox
                                        "
                                    >
                                        <path :d="selectedIcon.icon.svg"></path>
                                    </svg>
                                </div>

                                <div class="icon" v-if="!selectedIcon.icon">
                                    <i class="fas fa-image"></i>
                                </div>
                            </div>

                            <DefaultButton
                                text="Escolher ícone"
                                icon="fa fa-upload"
                                style="
                                    font-size: 12px;
                                    padding: 6px 22px;
                                    width: 100%;
                                "
                                :outline="true"
                                :onClick="openModalIcons"
                            />
                        </div>
                    </Col>
                    <Col sm="8" md="8" lg="8" xl="8">
                        <p class="label">Detalhes do link</p>

                        <TheInput
                            id="link"
                            label="Nome"
                            placeholder="Insira o nome do link..."
                            class="mb-3"
                            v-model="name"
                        />

                        <Select
                            id="type"
                            label="Tipo"
                            placeholder="Selecione uma opção"
                            class="mb-3"
                            v-model="typeContent"
                            :options="contentTypeOptions"
                        />

                        <Select
                            id="content"
                            label="Conteúdo"
                            placeholder="Digite o nome ou selecione um..."
                            class="mb-3"
                            :options="getOptions"
                            v-if="typeContent != 'link'"
                            v-model="selectedContent"
                        />
                        <TheInput
                            v-else
                            id="Externallink"
                            label="Link"
                            placeholder="Insira o Link externo..."
                            class="mb-3"
                            v-model="link"
                        />
                    </Col>
                </Row>
            </div>

            <footer class="new-item__actions">
                <DefaultButton
                    text="Cancelar"
                    :outline="true"
                    :onClick="close"
                />
                <DefaultButton
                    text="Salvar"
                    status="success"
                    icon="fas fa-check"
                    :onClick="saveMenuItem"
                />
            </footer>
        </section>
    </Modal>
</template>

<script>
import DefaultButton from "../../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Modal from "../../../../../js/components/XgrowDesignSystem/Modals/Modal.vue";
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Row from "../../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../../js/components/XgrowDesignSystem/Utils/Col";
import Select from "../../../../../js/components/XgrowDesignSystem/Form/Select";
import TheInput from "../../../../../js/components/XgrowDesignSystem/Form/Input";

import { urlRegex } from "../../../../../js/components/XgrowDesignSystem/Extras/functions";

import { mapActions, mapState, mapStores } from "pinia";
import { useDesignConfigMenu } from "../../../../../js/store/design-config-menu.js";

export default {
    name: "NewItem",
    components: {
        DefaultButton,
        Modal,
        Title,
        Subtitle,
        Row,
        Col,
        Select,
        TheInput,
    },
    props: {
        selectedIcon: { type: Object, default: {} },
    },
    data() {
        return {
            selectedContent: "",
            link: "",
            name: "",
            typeContent: "",
        };
    },
    computed: {
        ...mapStores(useDesignConfigMenu),
        ...mapState(useDesignConfigMenu, [
            "contentTypeOptions",
            "courseOptions",
            "contentOptions",
        ]),
        getOptions() {
            if (this.typeContent == "course") return this.courseOptions;
            if (this.typeContent == "content") return this.contentOptions;
        },
    },
    methods: {
        close() {
            this.clearItem();
            document.querySelector('html').style.overflowY = 'auto';
            this.$emit("close");
        },
        openModalIcons() {
            this.$emit("openIconModal");
        },
        validate() {
            if (!this.name)
                throw new Error(
                    "Nome, Tipo ou Conteúdo não pode ficar em branco."
                );

            if (this.typeContent != "link") {
                if (!this.selectedContent)
                    throw new Error(
                        "Nome, Tipo ou Conteúdo não podem ficar em branco."
                    );
            } else {
                if (!this.link) throw new Error("Insira o link");

                if (!urlRegex(this.link))
                    throw new Error("O formato do link é invalido");
            }
        },
        clearItem() {
            this.selectedContent = "";
            this.link = "";
            this.name = "";
            this.typeContent = "";
        },
        saveMenuItem() {
            let newItem = {
                name: this.name,
                icon: this.selectedIcon.icon,
                iconCategory: this.selectedIcon.name,
                contentType: this.typeContent,
            };

            if (!this.selectedIcon.icon) {
                newItem.icon = {
                    name: "FaFile",
                    svg: "M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm160-14.1v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z",
                    viewBox: "0 0 384 512",
                };
                newItem.iconCategory = "font-awesome";
            }

            if (this.typeContent == "link") {
                newItem.link = this.link;
            }

            if (this.typeContent != "link") {
                newItem.contentId = this.selectedContent;
            }

            try {
                this.validate();
                this.$emit("newItem", newItem);
                this.clearItem();
                this.$emit("close");
            } catch (e) {
                errorToast(
                    "Algum erro aconteceu!",
                    e.response?.data.error.message ??
                        e.message ??
                        "Não foi possível receber os ícones."
                );
            }
        },
    },
};
</script>

<style lang="scss" scoped>
.new-item {
    padding: 40px 52px;

    &__header {
        border-bottom: 1px solid rgba(#c4c4c4, 0.15);
        padding-bottom: 20px;
    }

    &__body {
        padding: 16px 0 40px;
        border-bottom: 1px solid rgba(#c4c4c4, 0.15);
    }

    &__actions {
        padding-top: 40px;
        display: flex;
        justify-content: center;
        gap: 22px;
    }
}

.title {
    font-size: 18px;
    line-height: 25px;
    color: #ffffff;

    & > * {
        color: #9b9b9b;
    }
}

.subtitle {
    font-size: 18px;
    line-height: 25px;
}

.label {
    color: #ffffff;
    font-size: 18px;
    line-height: 25px;
    font-weight: 600;
}

.icon {
    color: #9b9b9b;
    font-size: 36px;
    line-height: 36px;

    & > svg {
        width: 36px;
        height: 36px;
    }

    &__wrapper {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
}

.thumb {
    width: 150px;
    height: 150px;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #222429;
    border-radius: 6px;
    margin-top: 16px;
}
</style>
