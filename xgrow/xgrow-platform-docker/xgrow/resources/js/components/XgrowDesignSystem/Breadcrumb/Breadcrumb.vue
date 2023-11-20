<template>
    <nav class="xgrow-breadcrumb" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <template v-for="(item, i) in items" :key="i">
                <template v-if="item.isVueRouter">
                    <li class="breadcrumb-item">
                        <router-link :to="item.link">
                            <a :href="void(0)" v-if="item.link">{{ item.title }}</a>
                            <span v-else>{{ item.title }}</span>
                        </router-link>
                    </li>
                </template>
                <template v-else>
                    <li class="breadcrumb-item">
                        <a :href="item.link" v-if="item.link">{{ item.title }}</a>
                        <span v-else>{{ item.title }}</span>
                    </li>
                </template>
            </template>
        </ol>
    </nav>
</template>

<script>
export default {
    name: "Breadcrumb",
    props: {
        items: {type: Array, required: true}
    }
};
</script>

<style lang="scss" scoped>
.xgrow-breadcrumb {
    display: flex;
    align-items: center;
    margin: 1rem 0 0 0;

    .breadcrumb {
        display: flex;
        flex-wrap: wrap;
        padding: 0;
        margin-bottom: 1rem;
        list-style: none;
        gap: .5rem;

        .breadcrumb-item {
            background-color: #4b4b4b;
            color: #ffffff;
            min-width: 64px;
            padding: 0 12px;
            text-align: center;
            margin: 2px;
            height: 24px;
            border-radius: 4px;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 25%);

            &:last-of-type {
                background-color: #93bc1e;
                color: #ffffff;
                margin: auto 2px;
            }
        }
    }
}
</style>
