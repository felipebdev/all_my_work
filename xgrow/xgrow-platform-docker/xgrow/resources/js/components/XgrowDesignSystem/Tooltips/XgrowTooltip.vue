<!-- Used https://atomiks.github.io/tippyjs/ for create this plugin -->
<template>
    <span :id="id">
        <span v-if="icon" v-html="icon" :style="`color:${iconColor}`" class="icon"></span>
        {{ text }}
    </span>
</template>

<script>
import tippy from "tippy.js";
import 'tippy.js/dist/tippy.css';

export default {
    name: "XgrowTooltip",
    props: {
        /**
         * Id IS REQUIRED IMPORTANT
         */
        id: {
            type: String,
            required: true
        },
        /**
         * Tooltip is a text tip
         */
        tooltip: {
            type: String,
            required: true
        },
        /**
         * Position, used for placement
         * @values top, right, bottom, left
         */
        position: {
            type: String,
            default: 'right'
        },
        /**
         * If Tooltip content accept HTML
         **/
        allowHTML: {
            type: Boolean,
            default: false
        },
        /**
         * Text if use only text
         */
        text: {
            type: String,
            default: ''
        },
        /**
         * Icon is a complet html by default used information
         */
        icon: {
            type: String,
            default: null
        },
        /**
         * Icon color in HEX
         */
        iconColor: {
            type: String,
            default: '#7E8495'
        },
    },
    mounted() {
        tippy(`#${this.id}`, {
            content: this.tooltip,
            placement: this.position,
            allowHTML: this.allowHTML
        });
    }
}
</script>

<style lang="scss" scoped>
.icon{
    cursor: pointer;
}
</style>
