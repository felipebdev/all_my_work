<template>
    <div class="select-title__wrapper" @mouseleave="show = false">
        <button class="select-title__button" :id="id" @click="show = !show">
            <div class="d-flex gap-1 align-items-center">
                <i class="fas fa-text select-title__icon"></i>

                <div class="select-title__placeholder" v-if="value">
                    {{ `${value} ${rowOrColumn}${value > 1 ? 's' : ''}` }}
                </div>

                <div class="select-title__placeholder" v-else>
                    {{ placeholder }}
                </div>
            </div>

            <i class="fa fa-chevron-down"></i>
        </button>
        <div class="select-title__menu" v-if="show">
            <a
                class="select-title__item"
                :key="item"
                @click="sendValue(item)"
                v-for="item in (1, limit)"
            >
                {{ `${item} ${rowOrColumn}${item > 1 ? 's' : ''}` }}
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: "SelectStatus",
    props: {
        id: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: "column"
        },
        value: {
            type: Number,
            default: 2
        },
        placeholder: {
            type: String,
            default: "Selecione uma opção"
        },
        limit: {
            type: Number,
            default: 100
        }
    },
    data() {
        return {
            show: false
        }
    },
    computed: {
        rowOrColumn() {
            if (this.type == "column") return 'coluna';
            if (this.type == "row") return 'linha';
        },
    },
    methods: {
        sendValue(payload) {
            this.show = false;
            this.$emit('update:modelValue', payload);
            this.$emit('select', payload);
        }
    }
};
</script>

<style lang="scss" scoped>
    .select-title {
        &__wrapper {
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            max-width: 160px;
            width: 100%;
            position: relative;
        }

        &__button {
            align-items: center;
            background: #252932;
            border-radius: 8px;
            border: 1px solid #646D85;
            color: #FFFFFF;
            display: flex;
            font-size: 14px;
            height: 38px;
            justify-content: space-between;
            line-height: 1.6;
            padding: 8px;
            width: 100%;
        }

        &__icon {
            color: #93BC1E;
        }

        &__menu {
            align-items: flex-start;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            top: 38px;
            display: flex;
            flex-direction: column;
            left: 0;
            position: absolute;
            width: 100%;
            z-index: 1;
            height: 300px;
            overflow-y: auto;
        }

        &__item {
            color: #FFFFFF;
            display: block;
            font-size: 14px;
            line-height: 1;
            padding: 8px;
            text-align: start;
            text-decoration: none;
            width: 100%;

            &:nth-child(even) { background: #333844; }

            &:nth-child(odd) { background: #252932; }

            &:last-child { border-radius: inherit; }

            &:hover { background: #333844; }
        }
    }
</style>
