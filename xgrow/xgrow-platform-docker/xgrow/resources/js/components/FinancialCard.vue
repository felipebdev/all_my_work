<template>
    <div class="xgrow-financial-card d-flex align-items-center gap-2" :style="style">
        <div>
            <p class="title">
                <template v-if="hasInfo">
                    <i class="fa fa-exclamation-circle" :title="tooltip" data-bs-toggle="tooltip" :id="id"
                       data-bs-placement="bottom"></i>
                </template>
                {{ title }}
            </p>
            <p class="subtitle" :style="[hasInfo ? 'margin-left: 19px' : '']">{{ subtitle }}</p>
        </div>
        <slot name="container"></slot>
    </div>
</template>

<script>
// import {Tooltip} from "bootstrap/dist/js/bootstrap.esm.min.js";

export default {
    name: "FinancialCard",
    props: {
        title: {required: false, default: "", type: [String, Number]},
        subtitle: {required: false, default: "", type: [String, Number]},
        borderColor: {required: false, default: "#FFFFFF", type: String},
        hasInfo: {default: false, type: Boolean},
        tooltip: {required: false, default: "", type: String}
    },
    data() {
        return {
            id: ""
        };
    },
    computed: {
        style() {
            return "border-color: " + this.borderColor;
        }
    },
    methods: {
        /** Activate tooltip for info */
        activateTooltip: function () {
            const tooltipEl = document.getElementById(this.id);
            if (tooltipEl) {
                this.$nextTick(() => {
                    new Tooltip(tooltipEl);
                });
            }
        }
    },
    created() {
        this.id = Date.now().toString(36) + Math.random().toString(36).substring(2);
    },
    mounted() {
        // this.activateTooltip();
    }
};
</script>

<style lang="scss" scoped>
.xgrow-financial-card {
    min-width: 200px;
    /*height: 70px;*/
    background-color: transparent;
    border: 1px solid;
    border-radius: 8px;
    padding: 12px;
    color: #FFFFFF;

    div {
        .title {
            font-size: 16px;

            i {
                color: #7E8495;
                cursor: pointer;
            }
        }

        .subtitle {
            font-size: 14px;
            color: #E7E7E7;
        }
    }
}
</style>
