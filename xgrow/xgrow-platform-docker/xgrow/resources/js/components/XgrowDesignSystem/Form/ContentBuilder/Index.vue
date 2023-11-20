<template>
    <div class="preview-widgets__wrapper">
        <div
            :style="{ height: height ? height + 'px' : 'auto' }"
            class="preview-widgets"
        >
            <component
                :data="activeModal.data"
                :is="`modal-${activeModal.type}`"
                :isEdit="activeModal.isEdit"
                :isOpen="activeModal.isOpen"
                :key="activeModal.type"
                @close="closeModal(activeModal)"
                @update="updateWidget"
                @save="addWidget($event, activeModal.type)"
                v-if="activeModal.type"
            />

            <draggable
                :list="widgets"
                @end="changeOrder"
                @start="drag = true"
                ghost-class="ghost"
                group="widgets"
                handle=".btn-widget-drag"
                v-if="widgets && widgets.length"
                item-key="position"
            >
                <template #item="{ element }">
                    <span>
                        <component
                            v-bind="element"
                            :is="`widget-${getWidgetType(element)}`"
                            @delete="deleteWidget(element.position)"
                            @edit="openModal(getWidgetType(element), element)"
                            class="mb-2"
                        />
                    </span>
                </template>
            </draggable>

            <Title
                :isFormTitle="true"
                class="preview-widgets__title"
                v-else
            >
                Selecione um tipo de conteúdo para começar:
            </Title>

            <nav class="preview-widgets__menu mb-2" v-if="widgets && !widgets.length || include">
                Inserir mídia:
                <ul class="preview-widgets__list">
                    <li
                        @click="openModal(item.widget)"
                        class="preview-widgets__item"
                        v-for="item in previewIcons" :key="item.widget"
                    >
                        <img class="icon" :src="`/xgrow-vendor/assets/img/icons/${item.icon}`" :alt="item.alt" />
                        <div class="hover-tooltip">{{item.alt}}</div>
                    </li>
                </ul>
            </nav>

            <button v-if="widgets && widgets.length != 0" class="preview-widgets__active-nav" @click="() => include = true">
                <i class="fas fa-plus"></i>
            </button>

        </div>
        <div
            @drag="resize"
            @dragstart="removeGhost"
            class="preview-widgets__resizer"
            draggable="true"
        >
            <div class="preview-widgets__resizer__icon"></div>
        </div>
    </div>
</template>

<script>
import Title from '../../Typography/Title.vue';
import WidgetText from './widgets/TextPreview.vue';
import WidgetTitle from './widgets/TitlePreview.vue';
import WidgetLink from './widgets/LinkPreview.vue';
import WidgetFile from './widgets/FilePreview.vue';
import WidgetImage from './widgets/ImagePreview.vue';
import WidgetList from './widgets/ListPreview.vue';
import WidgetTable from './widgets/TablePreview.vue';
import WidgetAlert from './widgets/AlertPreview.vue';
import WidgetAudio from './widgets/AudioPreview.vue';
import WidgetVideo from './widgets/VideoPreview.vue';
import ModalLink from './modals/ModalLink.vue';
import ModalText from './modals/ModalText.vue';
import ModalFile from './modals/ModalFile.vue';
import ModalImage from './modals/ModalImage.vue';
import ModalList from './modals/ModalList.vue';
import ModalAlert from './modals/ModalAlert.vue';
import ModalTitle from './modals/ModalTitle.vue';
import ModalTable from './modals/ModalTable.vue';
import ModalAudio from './modals/ModalAudio.vue';
import ModalVideo from './modals/ModalVideo.vue';
import draggable from 'vuedraggable';

export default {
    name: "preview-widgets",
    components: {
        Title,
        WidgetText,
        WidgetTitle,
        WidgetLink,
        WidgetFile,
        WidgetImage,
        WidgetList,
        WidgetTable,
        WidgetAlert,
        WidgetAudio,
        WidgetVideo,
        ModalLink,
        ModalText,
        ModalFile,
        ModalImage,
        ModalList,
        ModalAlert,
        ModalTitle,
        ModalTable,
        ModalAudio,
        ModalVideo,
        draggable
    },
    props: {
        content: { type: Object, required: true }
    },
    data() {
        return {
            height: 0,
            activeModal: {
                isEdit: false,
                isOpen: false,
                type: "",
            },
            widgets: [],
            previewIcons: [
                { icon: "mdi-format-title.svg", widget: "title", alt: 'Inserir título' },
                { icon: "mdi-format-size.svg", widget: "text", alt: 'Inserir texto' },
                { icon: "mdi-link-variant.svg", widget: "link", alt: 'Inserir link externo' },
                { icon: "mdi-file-outline.svg", widget: "file", alt: 'Inserir arquivo' },
                { icon: "mdi-alert-outline.svg", widget: "alert", alt: 'Inserir alerta' },
                { icon: "mdi-image-outline.svg", widget: "image", alt: 'Inserir imagem' },
                { icon: "mdi-list-bulleted.svg", widget: "list", alt: 'Inserir lista' },
                { icon: "mdi-table.svg", widget: "table", alt: 'Inserir tabela' },
                { icon: "mdi-microphone-outline.svg", widget: "audio", alt: 'Inserir áudio' },
                { icon: "mdi-video-outline.svg", widget: "video", alt: 'Inserir vídeo' },
            ],
            include: false
        }
    },
    watch: {
        content(newData) { this.widgets = newData.widgets; }
    },
    methods: {
        removeGhost(e) {
            e.dataTransfer.setDragImage(new Image(), 0, 0);
        },
        resize(e) {
            const element = document.querySelector('.preview-widgets');
            this.height = element.scrollHeight + e.offsetY;
        },
        changeOrder() {
            this.widgets.forEach((widget, index) => (widget.position = index + 1))
        },
        closeModal() {
            this.activeModal.type = "";
            this.activeModal.isOpen = false;
            this.activeModal.isEdit = false;

            delete this.activeModal.data;
        },
        openModal(type, data = null) {
            this.activeModal.type = type;
            this.activeModal.isOpen = true;

            if (data) {
                this.activeModal.data = data;
                this.activeModal.isEdit = true;
            }
        },
        addWidget(data, type) {
              const widgets = {
                title: { text: data.text, text_type: data.text_type, position: this.widgets.length + 1, type: 'text' },
                text: { text: data.text, text_type: data.text_type, position: this.widgets.length + 1, type: 'text' },
                alert: { alert_title: data.alert_title, alert_description: data.alert_description, position: this.widgets.length + 1, type: 'alert' },
                link: { external_link_url: data.external_link_url, external_link_title: data.external_link_title, useExternalOAuthToken: data.useExternalOAuthToken, position: this.widgets.length + 1, type: 'externalLink' },
                list: { html: data.html, position: this.widgets.length + 1, type: 'HTML' },
                table: { table_data: data.table_data, position: this.widgets.length + 1, type: 'table' },
                audio: { audio_url: data.audio_url, position: this.widgets.length + 1, type: 'audio' },
                video: { video_url: data.video_url, position: this.widgets.length + 1, type: 'video' },
                image: { image_url: data.image_url, position: this.widgets.length + 1, type: 'image' },
            }

            if (type == "file") {
                widgets.file = {
                    File: { id: data.File.id, name: data.File.name, storage_link: data.File.storage_link },
                    file_id: data.File.id,
                    position: this.widgets.length + 1,
                    type: 'file'
                };
            };

            this.widgets.push(widgets[type]);
            this.include = false;
            this.closeModal();
        },
        updateWidget(data) {
            this.widgets[data.position - 1] = data;

            this.closeModal();
        },
        deleteWidget(position) {
            this.widgets.splice(position - 1, 1);

            successToast("Ação realizada", "Widget removido com sucesso");

            this.widgets.forEach((item, i) => item.position = i + 1);
        },
        getWidgetType(data) {
            if (!data) return;

            const { html,text_type, type } = data;

            if (type == 'text' && text_type != 'body') return 'title';
            if (type == 'text' && text_type == 'body') return 'text';
            if (type == 'alert') return 'alert';
            if (type == 'externalLink' || type == 'link') return 'link';
            if (type == 'HTML' && html.includes('li')) return 'list';
            if (type == 'table') return 'table';
            if (type == 'audio') return 'audio';
            if (type == 'video') return 'video';
            if (type == 'image') return 'image';
            if (type == 'file') return 'file';
        },
    },
    mounted() {
        this.widgets = this.content.widgets;
    }
}
</script>

<style lang="scss" scoped>
    .preview-widgets {
        background: #252932;
        margin-bottom: 8px;
        min-height: 400px;
        overflow: auto;
        padding: 14px;
        position: relative;
        width: 100%;

        &__wrapper {
            display: flex;
            flex-direction: column;
            position: relative;
        }

        &__menu {
            background: #2A2E39;
            padding: 20px 0;
            border-radius: 4px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        &__list {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 0;
        }

        &__item {
            display: block;
            width: 24px;
            height: 24px;
            cursor: pointer;
            color: #E7E7E7;
            position: relative;

            & > .hover-tooltip {
                color: #E7E7E7;
                background: #222429;
                border-radius: 8px;
                padding: 4px 8px;
                position: absolute;
                top: -30px;
                left: 50%;
                transform: translateX(-50%);
                display: none;
                width: 180px;
                text-align: center;
                z-index: 1;
            }

            &:last-child {
                & > .hover-tooltip {
                    left: initial;
                    transform: translateX(-60%);
                }
            }

            &:hover {

                & > .icon {
                    filter: brightness(0) saturate(100%) invert(78%) sepia(30%) saturate(1378%) hue-rotate(24deg) brightness(91%) contrast(76%);
                }

                & > .hover-tooltip { display: block; }
            }
        }

        &__title {
            color: #646D85;
            display: flex;
            justify-content: center;
            margin-bottom: 16px;
        }

        &__active-nav {
            align-items: center;
            border-radius: 4px;
            border: 2px dashed #646D85;
            display: flex;
            height: 32px;
            justify-content: center;
            width: 100%;
            background: transparent;
            color: #646D85;
        }

        &__resizer {
            width: 100%;
            height: 14px;
            background: #222429;
            border-bottom: 1px solid white;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: s-resize;
            position: absolute;
            bottom: 0;
            left: 0;

            &:active { cursor: s-resize; }

            &__icon {
                width: 22px;
                height: 1px;
                background: white;
                position: relative;

                &::before, &::after {
                    display: block;
                    content: '';
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    background: inherit;
                }

                &::before { top: -4px; }

                &::after { top: 4px; }
            }
        }
    }
</style>
