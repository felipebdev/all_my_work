<template>
    <Col sm="12" md="12" lg="4" xl="4">
    <Row class="e-container gap-4 pb-3">
        <Col class="e-container__title">
        <Title is-form-title icon="fas fa-list" icon-color="#FFFFFF" icon-bg="transparent" class="my-2">
            Elementos da página inicial
        </Title>
        </Col>
        <Col>
        <Subtitle is-small>
            <b>DESTAQUE</b>
        </Subtitle>
        <ElementMenu title="Banner de destaque" icon="fas fa-image" />
        </Col>
        <Col>
        <Subtitle is-small>
            <b>GRUPOS DE CONTEÚDO</b>
        </Subtitle>
        <draggable :list="groupContentWidgets" item-key="title" @start="drag = true" ghost-class="ghost"
            @end="drag = false" :group="{ name: 'widgets', pull: 'clone', put: false }" :sort="false" handle=".handle"
            :clone="cloneItem">
            <template #item="{ element }">
                <ElementMenu :title="element.title" :icon="element.icon" class="handle" />
            </template>
        </draggable>
        </Col>
        <Col>
        <Subtitle is-small>
            <b>WIDGET</b>
        </Subtitle>
        <draggable :list="commonWidgets" item-key="title" @start="drag = true" ghost-class="ghost" @end="drag = false"
            :group="{ name: 'widgets', pull: 'clone', put: false }" :sort="false" handle=".handle" :clone="cloneItem">
            <template #item="{ element }">
                <ElementMenu :title="element.title" :icon="element.icon" class="handle" />
            </template>
        </draggable>
        </Col>
    </Row>
    </Col>
</template>

<script>
import Col from "../../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Row from "../../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Title from "../../../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import ElementMenu from "./ElementMenu.vue";
import draggable from 'vuedraggable';
import LastAccess from "./widgets/LastAccess.vue";
import XgrowTooltip from "../../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip.vue";

export default {
    name: "ElementsContainer",
    components: { ElementMenu, Subtitle, Title, Row, Col, draggable, LastAccess, XgrowTooltip },
    data() {
        return {
            drag: false,
            groupContentWidgets: [
                { title: "Grupo (esteira)", icon: "fas fa-layer-group", type: "contentGroup", widgetName: "contentGroup" },
                { title: "Seção", icon: "fas fa-columns", type: "widget", widgetName: "section" },
            ],
            commonWidgets: [
                { title: "Últimos acessos", icon: "fas fa-clock", type: "widget", widgetName: "lastWatched" },
                { title: "Top mais assistidos", icon: "fas fa-trophy", type: "widget", widgetName: "topWatched" },
                { title: "Novidades", icon: "fas fa-newspaper", type: "widget", widgetName: "news" },
            ],
        }
    },
    methods: {
        createUUID() {
            let s = [];
            let hexDigits = "0123456789abcdef";
            for (var i = 0; i < 36; i++) {
                s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
            }
            s[14] = "4"; // bits 12-15 of the time_hi_and_version field to 0010
            s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1); // bits 6-7 of the clock_seq_hi_and_reserved to 01
            s[8] = s[13] = s[18] = s[23] = "-";
            return s.join("");
        },
        cloneItem(item) {
            if (item.widgetName === 'contentGroup') {
                return {
                    _id: this.createUUID(),
                    position: 0,
                    type: item.type,
                    groupTitle: "",
                    groupThumbStyle: "horizontal",
                    groupItems: [],
                    isNew: true
                };
            }
            if (item.widgetName === 'section') {
                return {
                    _id: this.createUUID(),
                    position: 0,
                    type: item.type,
                    widgetName: item.widgetName,
                    groupThumbStyle: "horizontal",
                    sectionId: 0,
                    itemsCount: 1,
                    isNew: true
                };
            }
            if (item.widgetName === 'news') {
                return {
                    _id: this.createUUID(),
                    position: 0,
                    type: item.type,
                    widgetName: item.widgetName,
                    newsCount: 1,
                    isNew: true
                };
            }
            if (item.widgetName === 'topWatched') {
                return {
                    _id: this.createUUID(),
                    position: 0,
                    type: item.type,
                    widgetName: item.widgetName,
                    forceTopWatched: false,
                    topWatchedForceItems: [],
                    isNew: true
                };
            }
            if (item.widgetName === 'lastWatched') {
                return {
                    _id: this.createUUID(),
                    position: 0,
                    type: item.type,
                    widgetName: item.widgetName,
                    lastWatchedCount: 1,
                    isNew: true
                };
            }
        },
    }
}
</script>

<style lang="scss" scoped>
.e-container {
    background-color: #222429;
    border-radius: 10px;
    min-width: 309px;

    &__title {
        background-color: #93bc1e;
        border-radius: 10px 10px 0 0;
        height: 46px;
    }
}
</style>
