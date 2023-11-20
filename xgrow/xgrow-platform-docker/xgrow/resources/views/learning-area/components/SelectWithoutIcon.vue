<template>
    <div class="xg-box" @mouseleave="show = false">
        <button class="xg-select x-dropdown-toggle" :id="id" @click="show = !show">
            <template v-if="modelValue === '' || modelValue === null">
                <span class="xg-name">{{ placeholder }}</span>
            </template>
            <template v-for="{ value, name } in options" :key="name">
                <div v-if="value.toString() === modelValue.toString()" class="xg-content">
                    <span class="xg-name">{{ name }}</span>
                </div>
            </template>
            <i class="xg-fa fa fa-chevron-down x-icon-arrow"></i>
        </button>
        <div class="xg-dropdown-menu" :class="show ? 'show' : ''">
            <a v-for="{ value, name } in options" :key="value" :value="value" @click="sendValue($event)">
                <div class="xg-content">
                    <span class="xg-name">{{ name }}</span>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: "SelectWithoutIcon",
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
            this.$emit('update:modelValue', el.target.getAttribute('value'))
            this.$emit('change', el.target.getAttribute('value'))
            this.$emit('input', el.target.getAttribute('value'))
        }
    }
};
</script>

<style lang="scss" scoped>
.xg-box {
    width: 200px;
    position: relative;

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
        border: 1px solid transparent;
        border-radius: 8px;
        box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 10px 0px, rgba(0, 0, 0, 0.1) 0px 0px 0px 1px;
        height: 38px;
        border: 1px solid #646D85;
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
        left: 0;

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

            &:hover {
                // filter: brightness(1.1);
                background-color: #333844;
            }

            &:last-child {
                border-radius: 0 0 8px 8px;
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
