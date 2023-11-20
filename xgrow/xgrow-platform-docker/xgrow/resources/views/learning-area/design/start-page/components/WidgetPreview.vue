<template>
    <Row class="widget-container">
        <BannerHighlight />
        <span class="p-0">
            <draggable :list="widgets" item-key="_id" @start="drag = true" ghost-class="ghost" @end="changeOrder"
                :disabled="blockDrag" group="widgets" handle=".btn-widget-drag">
                <template #item="{ element }">
                    <span>
                        <LastAccess v-if="element.type === 'widget' && element.widgetName === 'lastWatched'"
                            :item="element" :block-drag="!blockDrag" @delete-widget="deleteWidget" />
                        <LastUpdates v-if="element.type === 'widget' && element.widgetName === 'news'" :item="element"
                            :block-drag="!blockDrag" @delete-widget="deleteWidget" />
                        <TopWatched v-if="element.type === 'widget' && element.widgetName === 'topWatched'"
                            :item="element" :block-drag="!blockDrag" @delete-widget="deleteWidget" />
                        <ContentGroup v-if="element.type === 'contentGroup'" :item="element" :block-drag="!blockDrag"
                            @delete-widget="deleteWidget" />
                        <SectionContent v-if="element.type === 'widget' && element.widgetName === 'section'"
                            :item="element" :block-drag="!blockDrag" @delete-widget="deleteWidget" />
                    </span>
                </template>
            </draggable>
        </span>
    </Row>
</template>

<script>
import Row from "../../../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Col from "../../../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import BannerHighlight from "./widgets/BannerHighlight.vue";
import ContentGroup from "./widgets/ContentGroup.vue";
import SectionContent from "./widgets/SectionContent.vue";
import LastAccess from "./widgets/LastAccess.vue";
import LastUpdates from "./widgets/LastUpdates.vue";
import TopWatched from "./widgets/TopWatched.vue";
import { mapActions, mapState, mapStores } from "pinia";
import { useDesignStartPage } from "../../../../../js/store/design-start-page";
import draggable from 'vuedraggable';

export default {
    name: "WidgetPreview",
    components: { TopWatched, LastUpdates, LastAccess, SectionContent, ContentGroup, BannerHighlight, Col, Row, draggable },
    props: {
        blockDrag: { type: Boolean }
    },
    data() {
        return {
            drag: false,
        }
    },
    computed: {
        ...mapStores(useDesignStartPage),
        ...mapState(useDesignStartPage, ['widgets', 'loadingStore'])
    },
    methods: {
        ...mapActions(useDesignStartPage, ['deleteWidget']),
        changeOrder: function () {
            let position = 0;
            for (const item of this.widgets) {
                item.position = position;
                position++
            }
        },
    }
}
</script>

<style lang="scss" scoped>
.widget-container {
    min-width: 298px;
    background: #2A2E39;
    border: 2px dashed #626775;
}
</style>
