<template>
    <div class="status-badge" :class="{'status-badge--published': status}">
        {{isPublished}}
    </div>
</template>

<script>
export default {
    name: "status-badge",
    props: {
        status: { type: Boolean, required: true }
    },
    computed: {
        isPublished() {
            return this.status ? 'PUBLICADO' : 'RASCUNHO';
        }
    }
}
</script>

<style lang="scss" scoped>
    .status-badge {
        align-items: center;
        background-color: #454a54;
        border-radius: 4px;
        color: #fff;
        display: flex;
        height: 27px;
        justify-content: center;
        width: 90px;

        &--published {
            background: #3D4640;
            color: #93bc1e;
        }
    }
</style>
