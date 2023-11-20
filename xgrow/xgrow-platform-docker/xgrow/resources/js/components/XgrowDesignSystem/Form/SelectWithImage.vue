<template>
    <div class="xg-box" @mouseleave="show = false">
        <button class="xg-select x-dropdown-toggle" :id="id" @click="show = !show" data-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <template v-if="modelValue === '' || modelValue === null">
                <span class="xg-name">{{ placeholder }}</span>
            </template>
            <template v-else v-for="{ value, name, img } in options">
                <div v-if="value === modelValue" class="xg-content" :key="value">
                    <img :src="img ?? 'https://las.xgrow.com/background-default.png'" :alt="name" class="xg-img-profile"
                        :class="value === 'new' ? 'xg-new' : ''">
                    <span class="xg-name">{{ name }}</span>
                </div>
            </template>
            <i class="xg-fa fa fa-chevron-down x-icon-arrow"></i>
        </button>
        <div class="xg-dropdown-menu" :class="show ? 'show' : ''">
            <a v-for="{ value, name, img } in options" :key="value" :value="value" @click="sendValue($event)">
                <div class="xg-content">
                    <img :src="img ?? 'https://las.xgrow.com/background-default.png'" :alt="name" class="xg-img-profile"
                        :class="value === 'new' ? 'xg-new' : ''">
                    <span class="xg-name">{{ name }}</span>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: "SelectWithImage",
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
            type: [String, Number, Boolean],
            default: ""
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
        sendValue: function (el) {
            this.show = false;
            const valueSelected = el.target.getAttribute('value');
            this.$emit('update:modelValue', valueSelected);
            this.$emit('change', valueSelected);
        }
    }
};
</script>

<style lang="scss" scoped>
.xg-box {
    position: relative;
    width: 200px;

    .xg-img-profile {
        pointer-events: none;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid #646D85;
        margin-right: 1rem;
    }

    .xg-new {
        width: 22px;
        height: 22px;
    }

    .xg-name {
        pointer-events: none;
        font-family: 'Open Sans';
        font-style: normal;
        font-weight: 400;
        font-size: .875rem;
        line-height: 1.25rem;
    }

    .xg-fa {
        pointer-events: none;
        font-size: 1.1rem;
        color: #93BC1E;
    }

    .xg-content {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        pointer-events: none;
    }

    .xg-select {
        position: relative;
        width: inherit;
        background-color: #252932;
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        padding: .3rem .6rem;
        cursor: pointer;
        border: 1px solid #646D85;
        border-radius: 8px;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 10px 0px, rgba(0, 0, 0, 0.1) 0px 0px 0px 1px;
        height: 38px;
    }

    .xg-dropdown-menu {
        position: absolute;
        width: inherit;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 10px 0px, rgba(0, 0, 0, 0.1) 0px 0px 0px 1px;
        border-radius: 8px;
        margin-top: 0;
        background-color: #252932;
        z-index: 1;

        visibility: hidden;
        display: none;
        opacity: 0;
        transition: opacity 0.3s, visibility 0.3s;

        max-height: 200px;
        overflow-y: overlay;

        a {
            display: flex;
            align-items: center;
            column-gap: .5rem;
            padding: .5rem;
            text-decoration: none;
            color: #FFFFFF;
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            font-size: .875rem;
            line-height: 1.25rem;
            cursor: pointer;
            border-bottom: 1px solid #333844;

            &:hover {
                background-color: #333844;
                color: #fff;
            }

            &:last-child {
                border-bottom: none;
            }
        }
    }

    .show {
        visibility: visible;
        display: inline-block;
        opacity: 1;
        transition: opacity 0.3s, visibility 0.3s;
    }
}
</style>
