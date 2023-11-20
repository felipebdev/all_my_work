<template>
    <div class="select-title">
        <div class="select-title__wrapper" @mouseleave="show = false">
            <button class="select-title__button" :id="id" @click="show = !show">
                <div class="d-flex gap-1 align-items-center">
                    <img src="/xgrow-vendor/assets/img/icons/mdi-format-title.svg" class="select-title__icon" />

                    <div class="select-title__placeholder" v-if="modelValue">
                        {{ modelValue.name }}
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
                    :key="value"
                    :value="value"
                    @click="sendValue({ value, name })"
                    v-for="{ value, name } in options"
                >
                    {{ name }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "select-title",
    props: {
        id: {
            type: String,
            required: true
        },
        options: {
            type: Array,
            default: () => []
        },
        modelValue: {
            type: Object,
            default: { value: 'h2', name: 'Título 2'}
        },
        placeholder: {
            type: String,
            default: "Selecione uma opção"
        },
    },
    data() {
        return {
            show: false
        }
    },
    methods: {
        sendValue(payload) {
            this.show = false;
            this.$emit('update:modelValue', payload);
            this.$emit('change', payload);
        }
    }
};
</script>

<style lang="scss" scoped>
    .select-title {
        width: 100%;

        &__wrapper {
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            max-width: 248px;
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
            filter: brightness(0) saturate(100%) invert(78%) sepia(30%) saturate(1378%) hue-rotate(24deg) brightness(91%) contrast(76%);
        }

        &__menu {
            align-items: flex-start;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            bottom: -90px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            left: 0;
            position: absolute;
            width: 100%;
            z-index: 1;
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

            &:hover { background: #93BC1E; }
        }
    }
</style>
