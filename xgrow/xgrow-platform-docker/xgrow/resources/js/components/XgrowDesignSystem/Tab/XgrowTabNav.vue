<template>
    <div class="xgrow-tabs nav nav-tabs" :class="class" :id="id">
        <template v-for="(item, i) in items" :key="i">
            <a class="xgrow-tab-item nav-item nav-link" :id="'nav-'+item.screen"
               :class="{active: item.screen === selected}"
               @click="changePage(item.screen)"
               href="javascript:void(0)">
                {{ item.title }}
            </a>
        </template>
    </div>
</template>

<script>
export default {
    name: "XgrowTabNav",
    props: {
        id: {type: String, required: true},
        items: {type: Array, required: true},
        startTab: {type: String, required: true},
        class: {type: String, required: false, default: "mb-3"},
    },
    data() {
        return {
            selected: ""
        };
    },
    emits: ["changePage"],
    watch: {
        startTab(tab) {
            this.selected = tab
        }
    },
    methods: {
        changePage: function (screen) {
            this.selected = screen;
            this.$emit("changePage", screen);
        }
    },
    mounted() {
        this.selected = this.startTab;
    }
};
</script>

<style scoped>
.xgrow-tabs {
    border-bottom: 1px solid #353A47;
}

.xgrow-tab-item {
    border-bottom: 2px solid #DFDFDF;
}
</style>
