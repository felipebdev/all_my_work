<template>
    <div class="alert alert-rounded" :class="variantClass" v-if="open">
        <i class="ti-user"></i>
        <slot>
            This is an example top alert. You can edit what u wish.
        </slot>

        <button type="button" class="close" aria-label="Close" @click="close">
            <span aria-hidden="true">×</span>
        </button>

    </div>

</template>

<script>
    export default {

        props: {
            variant: { default: 'success'}
        },

        components: {},

        data() {
            return {
                open: true,
            }
        },

        computed: {
            variantClass() {
                return `alert-${this.variant}`;
            },
        },

        mounted() {
            //
        },

        methods: {
            close() {
                this.open = false
            },
        }

    }
</script>

<style scoped>

</style>