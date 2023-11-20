<template>
    <div class="accordion-item">
        <h2 class="accordion-header" :id="id">
            <div class="accordion-button collapsed" v-if="hasHtmlHeader" :class="{ 'collapsed': isOpen }" type="button"
                :aria-expanded="true" :aria-controls="targetId">
                <slot name="header" />
                <span class="button-click" data-bs-toggle="collapse" :data-bs-target="`#${targetId}`"></span>
            </div>
            <button v-else class="accordion-button collapsed" :class="{ 'collapsed': isOpen }" type="button"
                data-bs-toggle="collapse" :data-bs-target="`#${targetId}`" :aria-expanded="true" :aria-controls="targetId">
                <span>{{ title }} - {{ subtitle }}</span>
            </button>
        </h2>
        <div :id="targetId" class="accordion-collapse collapse" :class="{ 'show': isOpen }" :aria-labelledby="id">
            <div class="accordion-body">
                <slot></slot>
            </div>
        </div>
    </div>
</template>

<script>
import PipeVertical from "../Utils/PipeVertical";

export default {
    name: "AccordionItem",
    props: {
        id: { type: String, required: true },
        targetId: { type: String, required: true },
        accordionId: { type: String, required: true },
        title: { type: String, required: false },
        subtitle: { type: String, required: false },
        isOpen: { type: Boolean, default: false },
        hasHtmlHeader: { type: Boolean, default: false },
    },
    components: { PipeVertical }
}
</script>

<style lang="scss" scoped>
.accordion-item {
    margin-bottom: 20px;
}

.accordion-body {
    background: #333844;
    border-radius: 8px;
}

.accordion-collapse {
    border: solid rgba(0, 0, 0, 0);
}

.accordion-button {
    background: #333844;
    border: 2px solid #646D85;
    border-radius: 8px;
    margin-bottom: -12px;
    text-align: left;
    cursor: default;
}

.button-click {
    position: absolute;
    right: 12px;
    height: 32px;
    background: transparent;
    width: 32px;
    cursor: pointer;
    z-index: 10;
}
</style>
