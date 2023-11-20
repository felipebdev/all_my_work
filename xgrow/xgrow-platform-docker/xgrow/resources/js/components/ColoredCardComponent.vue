<template>
    <div
        class="my-2"
        :class="[
            `col-xxl-${xxl}`,
            `col-xl-${xl}`,
            `col-lg-${lg}`,
            `col-md-${md}`,
            `col-sm-${sm}`,
            `col-${xs}`,
        ]"
    >
        <div class="colored-card"
            :style="{ borderColor }">
            <p class="main-content">{{ content }}</p>
            <p class="label">{{ label }}</p>
        </div>
    </div>
</template>

<script>
import bootstrapColumns from "./config/bootstrapColumnsProps";

export default {
    name: "colored-card-component",
    data() {
        return {};
    },
    props: {
        /* Text properties */
        content: {
            type: String,
            required: true
        },
        label: {
            type: String,
            required: true
        },

        /* Styling */
        borderColor: {
            type: String,
            required: true
        },

        /*
        Bootstrap/responsiviness
        tags properties
        */
        ...bootstrapColumns,
    },
};
</script>
<style lang="scss" scoped>
@import "../../sass/util.scss";

.colored-card {
    color: #ffffff;
    font-family: "Open Sans";
    padding: 1rem;
    background-color: transparent;
    border-radius: pxToRem(8px);
    border-width: 2px;
    border-style: solid;

    .main-content {
        width: 100%;
        word-break: normal;
        font-size: 1rem;
        font-weight: 700;
    }

    .label {
        width: 100%;
        word-break: normal;
        font-size: 0.8rem;
    }
}
</style>
