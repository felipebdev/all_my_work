<template>
    <div class="xgrow-card card-dark">
        <div class="container-header d-flex align-items-center justify-content-between pb-2 flex-column flex-md-row"
            :class="{ 'has-border': hasBorder }" v-if="!noHeader">
            <div class="col-12 col-md-8">
                <slot name="header-left"></slot>
            </div>
            <div class="col-12 col-md-4 text-start text-md-end">
                <slot name="header-right"></slot>
            </div>
        </div>
        <div class="container-content">
            <slot name="content"></slot>
        </div>
        <div class="container-footer">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: "Container",
    props: {
        hasFooter: { type: Boolean, default: false },
        hasBorder: { type: Boolean, default: false },
        noHeader: { type: Boolean, default: false },
    },
}
</script>

<style lang="scss" scoped>
.xgrow-card {
    padding: 24px 34px !important;

    ::v-deep('container-content') {
        hr {
            background: #414655;
            opacity: 1;
        }
    }

    :slotted(.container-footer__footer) {
        border-top: 1px solid #575757;

        button {
            width: 200px;
            margin: 0 0 15px 0;
        }
    }
}

.has-border {
    border-bottom: 1px solid #414655;
}
</style>
