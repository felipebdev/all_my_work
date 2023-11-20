<template>
    <div class="list-widget">
        <div class="list-widget__icon">
            <img class="btn-widget-drag" src="/xgrow-vendor/assets/img/widgets/svg/apps.svg"/>
        </div>

        <component :is="type" class="list-widget__list">
            <li class="list-widget__item" v-for="(item, i) in items" :key="i">
                {{item}}
            </li>
        </component>

        <div class="list-widget__actions">
            <span @click="$emit('edit')">
                <i class="fas fa-pen edit" />
            </span>
            <div class="pipe"></div>
            <span @click="$emit('delete')">
                <i class="fas fa-trash delete" />
            </span>
        </div>
    </div>
</template>

<script>
export default {
    name: "list-widget",
    props: {
        html: { type: String, required: true },
    },
    data() {
        return {
            items: [],
            type: 'ul'
        }
    },
    methods: {
        decodeList(html) {
            // check list type
            this.type = html.includes('ul') ? 'ul' : 'ol';
            // remove ul and li tags
            let parsedArr = html.replace(/((<ul>|<ol>)|<li>(.*)<\/li>|(<\/ul>|<\/ol>))/g, '$3');
            // split string with breaking line and remove empty items
            this.items = parsedArr.split('\n').filter(el => !!el);
        },
    },
    watch:{
        html() {
            this.decodeList(this.html);
        }
    },
    mounted() {
        this.decodeList(this.html);
    }
};
</script>

<style lang="scss" scoped>
.list-widget {
    align-items: center;
    background: #2A2E39;
    border-radius: 4px;
    color: #E7E7E7;
    display: flex;
    font-size: 14px;
    gap: 8px;
    line-height: 1.6;
    padding: 4px 8px;
    position: relative;
    width: 100%;

    &:hover {
        .list-widget__actions, .list-widget__icon { display: flex; }
    }

    &__icon {
        display: none;
        height: 24px;
        width: 24px;

        &:hover {
            cursor: grab;

            &:active { cursor: grabbing; }
        }

    }

    &__list { margin: 0; }

    ul > li { list-style-type: disc; }

    ol > li { list-style-type: decimal; }

    &__actions {
        align-items: center;
        background: linear-gradient(269.89deg, rgba(#333844, .85) 22.33%, rgba(#333844, 0) 90.13%);
        border-radius: inherit;
        display: none;
        gap: 8px;
        height: 100%;
        justify-content: flex-end;
        padding: 8px;
        position: absolute;
        right: 0;
        top: 0;
        width: calc(100% - 32px);

        .pipe {
            background:#222429;
            height: 14px;
            width: 1px;
        }

        .edit, .delete { cursor: pointer; }

        .edit { color: #ADDF45; }

        .delete { color: #F96C6C; }
    }
}
</style>
