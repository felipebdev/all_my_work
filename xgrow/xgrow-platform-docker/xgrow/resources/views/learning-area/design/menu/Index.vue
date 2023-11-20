<template>
    <div>
        <LoadingStore/>
        <Breadcrumb :items="breadcrumbs" class="mb-3"></Breadcrumb>

        <Container>
            <template v-slot:header-left>
                <Title>Menus</Title>
                <p>Organize, edite e reordene o menu de sua plataforma.</p>
            </template>
            <template v-slot:content>
                <Row>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                        <Subtitle style="font-weight: 600">
                            Escolha o tipo de menu
                        </Subtitle>

                        <div class="d-flex gap-4 my-2">
                            <RadioButton
                                id="vertical"
                                name="menuType"
                                option="vertical"
                                label="Menu vertical"
                                :checked="menuType == 'vertical'"
                                v-model="menuType"
                            />
                            <RadioButton
                                id="horizontal"
                                name="menuType"
                                option="horizontal"
                                label="Menu horizontal"
                                :checked="menuType == 'horizontal'"
                                v-model="menuType"
                            />
                        </div>

                        <Subtitle style="font-weight: 600">
                            Outras opções
                        </Subtitle>

                        <div class="d-flex gap-4 my-2">
                            <SwitchButton id="lives" v-model="enableLive">
                                Habilitar ambiente de lives
                            </SwitchButton>
                            <SwitchButton id="forum" v-model="enableForum" v-if="false">
                                Habilitar fórum
                            </SwitchButton>
                        </div>
                    </Col>
                    <Col sm="12" md="12" lg="12" xl="6" class="mt-4 mt-xl-0">
                        <div class="info-preview" v-if="false">
                            <img
                                src="/xgrow-vendor/assets/img/lacustom/xgrowzinho-alert.svg"
                                alt="Alerta. Para visualizar o preview, necessita de no mínimo uma resolução de 600 pixels."
                            />
                            <div class="info-preview__content">
                                <h3>Atenção!</h3>
                                <p>
                                    Para ter uma melhor visualização do preview,
                                    é necessário possuir uma resolução de no
                                    mínimo 600px. Em caso de dispositivos
                                    móveis, visualizar na horizontal.
                                </p>
                            </div>
                        </div>
                        <PreviewVertical
                            :menu="menu"
                            @newItem="openModalNewItem(true)"
                            @delete="openDeleteModal"
                            @editItem="openModalEditItem"
                            v-if="menuType == 'vertical'"
                        />
                        <PreviewHorizontal
                            :menu="menu"
                            @newItem="openModalNewItem(true)"
                            v-if="menuType == 'horizontal'"
                        />
                    </Col>
                </Row>
            </template>
            <template v-slot:footer>
                <div class="panel__footer">
                    <router-link :to="{ name: 'design-index' }">
                        <DefaultButton
                            text="Cancelar"
                            :outline="true"
                        ></DefaultButton>
                    </router-link>
                    <DefaultButton
                        text="Salvar"
                        status="success"
                        :onClick="save"
                    ></DefaultButton>
                </div>
            </template>
        </Container>

        <NewItem
            :isOpen="modal.newItem"
            :selectedIcon="selectedIcon"
            @close="openModalNewItem(false)"
            @openIconModal="openModalIcons(true)"
            @newItem="addNewItem"
            :key="newItemCount"
        />

         <EditItem
            :isOpen="modal.editItem"
            :newIcon="selectedIcon"
            @close="openModalEditItem(false)"
            @openIconModal="openModalIcons(true)"
            @editItem="addEditItem"
            :item="editItem"
            :key="editItemCount"
        />

        <SelectIcons
            :isOpen="modal.icons"
            @close="openModalIcons(false)"
            @selectedIcon="updateSelectedIcon"
            :key="newSelectIcon"
        />

        <ConfirmModal :is-open="modal.delete.open">
            <Title>Deseja continuar com a exclusão? </Title>
            <Row class="w-100">
                <Col class="modal-body__text">
                    <i class="modalIcon fas fa-exclamation-triangle" style="color: #E28A22; font-size: 3rem;"></i>
                </Col>
            </Row>

            <Row class="w-100">
                <Col>
                    <p class="text-center">Essa ação é irreversível!</p>
                </Col>
            </Row>

            <div class="modal-body__footer">
                <DefaultButton  text="Cancelar" outline :onClick="openDeleteModal"/>
                <DefaultButton  text="Confirmar" status="success" @click="deleteItem(modal.delete.id)"/>
            </div>
        </ConfirmModal>

    </div>
</template>

<script>
import Loading from "../../../../js/components/XgrowDesignSystem/Utils/Loading";
import Breadcrumb from "../../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import XgrowTabNav from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import XgrowTabContent from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import XgrowTab from "../../../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import PipeVertical from "../../../../js/components/XgrowDesignSystem/Utils/PipeVertical";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import RadioButton from "../../../../js/components/XgrowDesignSystem/Form/RadioButton";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import PreviewVertical from "./components/PreviewVertical";
import PreviewHorizontal from "./components/PreviewHorizontal";
import NewItem from "./modal/NewItem";
import SelectIcons from "./modal/SelectIcons";
import EditItem from "./modal/EditItem";
import axios from "axios";
import {mapActions, mapState, mapStores} from "pinia";
import {useDesignConfigMenu} from "../../../../js/store/design-config-menu.js"
import LoadingStore from '../../../../js/components/XgrowDesignSystem/Utils/LoadingStore';
import ConfirmModal from '../../../../js/components/XgrowDesignSystem/Modals/ConfirmModal';

export default {
    name: "DesignConfigMenu",
    components: {
        Loading,
        Breadcrumb,
        Container,
        DefaultButton,
        XgrowTabNav,
        XgrowTabContent,
        XgrowTab,
        Title,
        PipeVertical,
        Subtitle,
        Row,
        Col,
        RadioButton,
        SwitchButton,
        PreviewVertical,
        PreviewHorizontal,
        NewItem,
        SelectIcons,
        EditItem,
        LoadingStore,
        ConfirmModal
    },
    watch: {},
    data() {
        return {
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                {
                    title: "Design",
                    link: "/learning-area/design",
                    isVueRouter: true,
                },
                { title: "Menus", link: false },
            ],
            menuType: "vertical",
            menu: [],
            iconList: [],
            enableLive: false,
            enableForum: false,
            modal: {
                newItem: false,
                icons: false,
                editItem: false,
                delete: {
                    id: '',
                    open: false
                }
            },
            editItem: {},
            selectedIcon: {},
            newItemCount: 0,
            editItemCount: 0,
            newSelectIcon: 0,
        };
    },
    watch: {
        async enableLive(newState) {
            if (newState == undefined) return;

            const live = this.menu.filter(item => item.type == 'live')[0];
            const defaultLive = {
                icon: "FaSatelliteDish",
                iconCategory: "font-awesome",
                isExternalLink: false,
                link: "https://xgrow.com/",
                liveId: 0,
                position: this.menu.length + 1,
                title: "Lives",
                type: "live",
            }

            if (!newState) {
                if (live._id) await this.deleteItem(live._id);

                this.menu = this.menu.filter(item => item.type !== 'live');
            } else
                if(!live) this.menu.push(defaultLive);
        },
        async enableForum(newState) {
            if (newState == undefined) return;

            const forum = this.menu.filter(item => item.type == 'forum')[0];
            const defaultForum = {
                icon: "FaRegCommentAlt",
                iconCategory: "font-awesome",
                isExternalLink: false,
                link: "https://xgrow.com/",
                forumId: 0,
                position: this.menu.length + 1,
                title: "Fórum",
                type: "forum",
            }

            if (!newState) {
                if (forum._id) await this.deleteItem(forum._id);

                this.menu = this.menu.filter(item => item.type !== 'forum');
            } else
                if(!forum) this.menu.push(defaultForum);
        },

    },
    computed: {
        ...mapStores(useDesignConfigMenu),
        ...mapState(useDesignConfigMenu, ["axiosStore", "loadingStore","contentTypeOptions", "courseOptions", "contentOptions"]),
    },
    methods: {
        ...mapActions(useDesignConfigMenu, ["getAllProducerContent", "getIconList"]),
        async getData() {
            try {
                this.loadingStore.setLoading(true);
                const res = await axios.get(
                    `${this.axiosStore.axiosUrl}/producer/mainpage/menu`,
                    this.axiosStore.axiosHeader
                );
                const menu = res.data.data;

                this.menu = menu.map(item => {
                    if (item.type === 'live') item['liveId'] = 0;
                    if (item.type === 'forum') item['forumId'] = 0;
                    if (item.type === 'section') item['sectionId'] = item.sectionId;
                    if (item.type === 'course') item['courseId'] = item.courseId;
                    if (item.type === 'content') item['contentId'] = item.contentId;
                    if (item.type === 'externalLink') item['externalLinkId'] = 0;

                    return item;
                }).sort((a,b) => a.position - b.position);

                this.enableLive =
                    menu.filter((item) => item.type == "live").length > 0;
                this.enableForum =
                    menu.filter((item) => item.type == "forum").length > 0;

                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
            }
        },
        async save() {
            this.loadingStore.setLoading(true);

            const menu = this.menu.map((item, index) => {
                delete item.__v;
                delete item.updatedAt;
                delete item.platformId;
                delete item.createdAt;
                delete item.useExternalOAuthToken;
                delete item._id;

                const specialTypes = {
                    forum: 'forumId',
                    live: 'liveId'
                }

                if(item.type == 'live' || item.type == 'forum') {
                    delete item.courseId;
                    delete item.contentId;
                    delete item.externalLinkId;

                    item[specialTypes[item.type]] = 0;
                    item.type == 'live' ? delete item.forumId : delete item.liveId;
                } else {
                    delete item.forumId;
                    delete item.liveId;
                }

                if(item.isExternalLink) {
                    item.linkId = 0;
                }

                item.position = index + 1;

                return item;
            })

            try {
                this.loadingStore.setLoading(true);

                await axios.post(
                    `${this.axiosStore.axiosUrl}/producer/mainpage/menu`,
                    menu,
                    this.axiosStore.axiosHeader,
                );

                successToast('Sucesso', 'Menu atualizado com sucesso. A alteração pode levar até 5 minutos para refletir na Área de Aprendizado.');

            } catch (e) {
                errorToast(
                    "Algum erro aconteceu!",
                    e.response?.data.error.message ??
                        e.message ??
                        "Não foi possível receber os ícones."
                );
            }

            this.loadingStore.setLoading();
        },
        async deleteItem(id) {
            if(this.modal.delete.open) {
                this.modal.delete.open = false;
                this.loadingStore.setLoading(true);
                this.menu = this.menu.filter(item => item._id != id);
                this.modal.delete.id = ""
            }

            if (id) {
                try {
                    await axios.delete(
                        `${this.axiosStore.axiosUrl}/producer/mainpage/menu/${id}`,
                        this.axiosStore.axiosHeader
                    );
                }catch (e) {
                    errorToast(
                        "Algum erro aconteceu!",
                        e.response?.data.error.message ??
                            e.message ??
                            "Não foi possível receber os ícones."
                    );
                }
            }

            this.loadingStore.setLoading();
        },
        openModalIcons(active) {
            this.modal.icons = active
            if(!active) this.newSelectIcon++;
        },
        openModalNewItem(active) {
            this.modal.newItem = active
            if(!active) {
                this.selectedIcon = {};
                this.newItemCount++;
            }
        },
        openModalEditItem(item) {
            this.modal.editItem = !this.modal.editItem;
            this.editItem = item;

            if(!this.modal.editItem) {
                this.selectedIcon = {};
                this.editItem = {};
                this.editItemCount++;
            }
        },
        openDeleteModal(item) {
            this.modal.delete.open = !this.modal.delete.open;
            this.modal.delete.id = item._id
            if(!this.modal.delete.open) this.modal.delete.id = ""
        },
        updateSelectedIcon(icon) {
            this.newSelectIcon++;
            this.selectedIcon = icon
        },
        addNewItem(payload) {
            this.newItemCount++;
            this.selectedIcon = {};

            let newItem = {
                title: payload.name,
                icon: payload.icon.name,
                link: payload.link ?? "não requerido!",
                isExternalLink: !!payload.link,
                iconCategory: payload.iconCategory,
                type: payload.contentType,
                position: this.menu.length + 1
            }

            if(payload.contentType == 'link') {
                newItem.isExternalLink = true;
                newItem.externalLinkId = 0;
            }
            if(payload.contentType == 'course') {
                newItem.courseId = payload.contentId;
            }
            if(payload.contentType == 'content') {
                newItem.contentId = payload.contentId;
            }

            this.menu.push(newItem);
        },
        addEditItem(payload) {
            this.editItemCount++;
            const indexFromItemToEdit = this.menu.findIndex((item) => item.position == payload.position);

            this.menu[indexFromItemToEdit] = payload;
        }
    },
    async created() {
        this.loadingStore.setLoading(true);
        await this.axiosStore.setAxiosHeader();
        await this.getIconList();
        await this.getData();
        await this.getAllProducerContent();
        this.loadingStore.setLoading(false);
    },
};
</script>

<style lang="scss" scoped>
.panel {
    &__footer {
        display: flex;
        justify-content: space-between;
        padding: 24px 0 0;
        margin-top: 24px;
        border-top: 1px solid #414655;
    }
}
p {
    color: #ffffff !important;
}
</style>
